<?php
include '../db.php';
session_start();

if (!isset($_POST['quizID'])) {
    die("Quiz not specified.");
}

$quizID = intval($_POST['quizID']);
$studentID = $_SESSION['user_id'];
$tabSwitchCount = isset($_POST['tab_switch_count']) ? intval($_POST['tab_switch_count']) : 0;

// Detect if answers came from JSON (beacon/back-button) or normal form
$answers = [];
if (isset($_POST['answer'])) {
    $answers = is_array($_POST['answer']) 
        ? $_POST['answer'] 
        : json_decode($_POST['answer'], true);
}

// Helper: normalize text for comparison
function normalize($str) {
    return strtolower(trim(preg_replace('/\s+/', ' ', $str)));
}

// Get assessment_authorID
$stmt = $conn->prepare("
    SELECT assessment_authorID 
    FROM assessment_author 
    WHERE assessment_refID = ? AND assessment_type = 'quiz' LIMIT 1
");
$stmt->bind_param("i", $quizID);
$stmt->execute();
$res = $stmt->get_result();
if (!$row = $res->fetch_assoc()) {
    die("Assessment not found.");
}
$assessment_authorID = $row['assessment_authorID'];

// Prevent multiple submissions
$checkStmt = $conn->prepare("
    SELECT status 
    FROM student_assessments 
    WHERE student_id = ? AND assessment_authorID = ? LIMIT 1
");
$checkStmt->bind_param("ii", $studentID, $assessment_authorID);
$checkStmt->execute();
$checkRes = $checkStmt->get_result()->fetch_assoc();
if ($checkRes && $checkRes['status'] === 'submitted') {
    // Already submitted — always redirect to results
    echo "<script>
        localStorage.removeItem('quiz_{$quizID}_answers');
        localStorage.removeItem('quiz_{$quizID}_currentIndex');
        window.location.href = '../student/student-subject-landingpage.php';
    </script>";
    exit;
}

// Fetch quiz questions
$questions = [];
$qStmt = $conn->prepare("
    SELECT questionID, question_type, correct_answer 
    FROM quiz_questions 
    WHERE quizID = ?
");
$qStmt->bind_param("i", $quizID);
$qStmt->execute();
$qRes = $qStmt->get_result();
while ($r = $qRes->fetch_assoc()) {
    $questions[$r['questionID']] = [
        'type' => $r['question_type'],
        'correct' => trim($r['correct_answer'])
    ];
}

$totalQuestions = count($questions);
$correctCount = 0;

// Save answers
foreach ($questions as $questionID => $qdata) {
    $studentAnswer = isset($answers[$questionID]) ? trim($answers[$questionID]) : "";
    $selectedChoiceID = null;
    $answerText = null;

    if ($qdata['type'] === 'multiple') {
        if (is_numeric($studentAnswer)) {
            $selectedChoiceID = intval($studentAnswer);
            $choiceStmt = $conn->prepare("SELECT option_label FROM quiz_choices WHERE choiceID = ?");
            $choiceStmt->bind_param("i", $selectedChoiceID);
            $choiceStmt->execute();
            $choiceRes = $choiceStmt->get_result()->fetch_assoc();
            $answerText = $choiceRes ? $choiceRes['option_label'] : "";
        } else {
            $answerText = $studentAnswer;
        }
        if (normalize($qdata['correct']) === normalize($answerText)) {
            $correctCount++;
        }
    } else {
        $answerText = $studentAnswer;
        if (normalize($studentAnswer) === normalize($qdata['correct'])) {
            $correctCount++;
        }
    }

    // Insert answer only once
    $subCheckStmt = $conn->prepare("
        SELECT 1 FROM student_submissions 
        WHERE student_id = ? AND assessment_authorID = ? AND questionID = ? LIMIT 1
    ");
    $subCheckStmt->bind_param("iii", $studentID, $assessment_authorID, $questionID);
    $subCheckStmt->execute();
    if (!$subCheckStmt->get_result()->fetch_assoc()) {
        $insStmt = $conn->prepare("
            INSERT INTO student_submissions 
            (student_id, assessment_authorID, questionID, answer_text, selected_choiceID, submitted_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $insStmt->bind_param("iiisi", $studentID, $assessment_authorID, $questionID, $answerText, $selectedChoiceID);
        $insStmt->execute();
    }
}

$score = (float)$correctCount;

// Update assessment record
$updateStmt = $conn->prepare("
    UPDATE student_assessments
    SET status = 'submitted', submission_date = NOW(), score = ?, tabs_open = ?
    WHERE student_id = ? AND assessment_authorID = ?
");
$updateStmt->bind_param("diii", $score, $tabSwitchCount, $studentID, $assessment_authorID);
$updateStmt->execute();

// Always clear localStorage and redirect to results page
echo "<script>
    localStorage.removeItem('quiz_{$quizID}_answers');
    localStorage.removeItem('quiz_{$quizID}_currentIndex');
    alert('Quiz submitted successfully!');
    window.location.href = '../student/student-subject-landingpage.php';
</script>";
exit;
?>