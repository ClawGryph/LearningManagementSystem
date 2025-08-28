<?php
include '../db.php';
session_start();

if (!isset($_POST['quizID'])) {
    die("Quiz not specified.");
}

$quizID = intval($_POST['quizID']);
$studentID = $_SESSION['user_id'];
$tabSwitchCount = isset($_POST['tab_switch_count']) ? intval($_POST['tab_switch_count']) : 0;
$submitType = isset($_POST['submit_type']) ? $_POST['submit_type'] : 'manual';

// Parse answers safely
$answers = [];
if (isset($_POST['answer'])) {
    $raw = $_POST['answer'];
    if (is_array($raw)) {
        $answers = $raw;
    } else {
        $decoded = json_decode($raw, true);
        if ($decoded === null) {
            $decoded = json_decode(urldecode($raw), true);
        }
        $answers = is_array($decoded) ? $decoded : [];
    }
}

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

// Check if already submitted
$checkStmt = $conn->prepare("
    SELECT status 
    FROM student_assessments 
    WHERE student_id = ? AND assessment_authorID = ? LIMIT 1
");
$checkStmt->bind_param("ii", $studentID, $assessment_authorID);
$checkStmt->execute();
$checkRes = $checkStmt->get_result()->fetch_assoc();

if ($checkRes && $checkRes['status'] === 'submitted') {
    if ($submitType === 'auto') {
        exit; 
    } else {
        echo "<script>
            localStorage.removeItem('quiz_{$quizID}_answers');
            localStorage.removeItem('quiz_{$quizID}_currentIndex');
            localStorage.removeItem('quiz_{$quizID}_tabSwitchCount');
            localStorage.removeItem('quiz_{$quizID}_warnedOnce');
            window.location.href = '../student/student-subject-landingpage.php';
        </script>";
        exit;
    }
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

$conn->begin_transaction();

// Remove old submissions
$delStmt = $conn->prepare("
    DELETE FROM student_submissions
    WHERE student_id = ? AND assessment_authorID = ?
");
$delStmt->bind_param("ii", $studentID, $assessment_authorID);
$delStmt->execute();
$delStmt->close();

$totalQuestions = count($questions);
$correctCount = 0;

// Save answers
foreach ($questions as $questionID => $qdata) {
    $studentAnswer = isset($answers[$questionID]) ? trim($answers[$questionID]) : null;
    $selectedChoiceID = null;
    $answerText = null;

    if ($studentAnswer !== null && $studentAnswer !== "") {
        if ($qdata['type'] === 'multiple') {
            if (is_numeric($studentAnswer)) {
                $selectedChoiceID = intval($studentAnswer);
                $choiceStmt = $conn->prepare("SELECT option_label FROM quiz_choices WHERE choiceID = ?");
                $choiceStmt->bind_param("i", $selectedChoiceID);
                $choiceStmt->execute();
                $choiceRes = $choiceStmt->get_result()->fetch_assoc();
                $choiceStmt->close();
                $answerText = $choiceRes ? $choiceRes['option_label'] : $studentAnswer;
            } else {
                $answerText = $studentAnswer;
            }

            if ($answerText !== null && normalize($qdata['correct']) === normalize($answerText)) {
                $correctCount++;
            }

        } elseif ($qdata['type'] === 'truefalse') {
            if ($studentAnswer === "True" || $studentAnswer === "False") {
                $answerText = $studentAnswer;
            }

            if ($answerText !== null && normalize($answerText) === normalize($qdata['correct'])) {
                $correctCount++;
            }

        } else {
            // Identification / free-text
            $answerText = $studentAnswer;
            if (normalize($studentAnswer) === normalize($qdata['correct'])) {
                $correctCount++;
            }
        }
    }

    // Insert submission
    $ins = $conn->prepare("
        INSERT INTO student_submissions 
        (student_id, assessment_authorID, questionID, answer_text, selected_choiceID, submitted_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    // Correct handling of NULL
    if ($selectedChoiceID === null) {
        $ins->bind_param("iiiss", $studentID, $assessment_authorID, $questionID, $answerText, $selectedChoiceID);
        $selectedChoiceID = null;
    } else {
        $ins->bind_param("iiisi", $studentID, $assessment_authorID, $questionID, $answerText, $selectedChoiceID);
    }

    $ins->execute();
    $ins->close();
}

$score = (float)$correctCount;

// Finalize assessment
$updateStmt = $conn->prepare("
    UPDATE student_assessments
    SET status = 'submitted', submission_date = NOW(), score = ?, tabs_open = ?
    WHERE student_id = ? AND assessment_authorID = ?
");
$updateStmt->bind_param("diii", $score, $tabSwitchCount, $studentID, $assessment_authorID);
$updateStmt->execute();
$updateStmt->close();
$conn->commit();

// Response
if ($submitType === 'auto') {
    exit;
} else {
    echo "<script>
        localStorage.removeItem('quiz_{$quizID}_answers');
        localStorage.removeItem('quiz_{$quizID}_currentIndex');
        localStorage.removeItem('quiz_{$quizID}_tabSwitchCount');
        localStorage.removeItem('quiz_{$quizID}_warnedOnce');
        alert('Quiz submitted successfully!');
        window.location.href = '../student/student-subject-landingpage.php';
    </script>";
    exit;
}
?>