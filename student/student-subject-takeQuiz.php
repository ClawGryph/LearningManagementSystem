<?php
include '../db.php';
session_start();

if (!isset($_GET['quizID'])) {
    die("Quiz not specified.");
}

$quizID = intval($_GET['quizID']);
$studentID = $_SESSION['user_id'];
$instructor_courseID = $_SESSION['instructor_courseID'];
$quizTitle = "";

$sql = "
    SELECT q.title, qq.questionID, qq.question, qq.question_type, qq.correct_answer,
           qc.choiceID, qc.option_label, qc.option_text,
           aa.assessment_time
    FROM quiz_questions qq
    LEFT JOIN quiz_choices qc ON qq.questionID = qc.questionID
    JOIN quizzes q ON qq.quizID = q.quizID
    JOIN assessment_author aa ON aa.assessment_refID = qq.quizID
    JOIN student_assessments sa ON sa.assessment_authorID = aa.assessment_authorID
    WHERE qq.quizID = ?
      AND sa.student_id = ?
      AND aa.instructor_courseID = ?
      AND aa.assessment_type = 'quiz'
    ORDER BY qq.questionID, qc.option_label
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $quizID, $studentID, $instructor_courseID);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
$assessment_time = 0;

while ($row = $result->fetch_assoc()) {
    if ($assessment_time == 0) {
        $assessment_time = (int)$row['assessment_time'];
    }
    if (empty($quizTitle)) {
        $quizTitle = $row['title'];
    }

    $qid = $row['questionID'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'questionID' => $row['questionID'],
            // escape so HTML is displayed as text
            'question'   => htmlspecialchars($row['question'], ENT_QUOTES, 'UTF-8'),
            'type'       => $row['question_type'],
            'correct'    => $row['correct_answer'],
            'choices'    => []
        ];
    }
    if ($row['choiceID']) {
        $questions[$qid]['choices'][] = [
            'choiceID' => $row['choiceID'],
            'label'    => $row['option_label'],
            // escape so HTML is displayed as text
            'text'     => htmlspecialchars($row['option_text'], ENT_QUOTES, 'UTF-8')
        ];
    }
}

// Timer handling
if (!isset($_SESSION['quiz_start_time'][$quizID])) {
    $_SESSION['quiz_start_time'][$quizID] = time();
}

$elapsed = time() - $_SESSION['quiz_start_time'][$quizID];
$remaining = ($assessment_time * 60) - $elapsed;
if ($remaining <= 0) {
    $remaining = 0;
}
$duration = $remaining;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student | Quiz</title>
<link rel="stylesheet" href="../css/normalize.css">
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="fullPageContainer">
    <div class="fullPageContent">
        <div class="fullPageHeader">
            <div class="takeQuizTitle" id="takeQuizTitle"><?= htmlspecialchars($quizTitle) ?></div>
            <div id="timer"></div>
        </div>
        <form id="quizForm" class="takeQuizForm" action="../action/submitQuiz.php" method="POST">
            <input type="hidden" name="quizID" value="<?= htmlspecialchars($quizID) ?>">
            <div id="question-container"></div>
            <div style="margin-top:20px;">
                <button type="button" id="prevBtn" disabled>Previous</button>
                <button type="button" id="nextBtn">Next</button>
                <button type="submit" id="submitBtn" style="display:none;">Submit Quiz</button>
            </div>
        </form>
    </div>
</div>

<script>
    const quizID = <?= (int)$quizID ?>;
    const questions = <?= json_encode(array_values($questions)) ?>;
    let duration = <?= $duration ?>;
</script>
<script src="../js/student-takeQuiz.js"></script>

</body>
</html>