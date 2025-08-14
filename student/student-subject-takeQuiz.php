<?php
include '../db.php';
session_start();

if (!isset($_GET['quizID'])) {
    die("Quiz not specified.");
}

$quizID = intval($_GET['quizID']);
$studentID = $_SESSION['user_id'];
$instructor_courseID = $_SESSION['instructor_courseID'];

$sql = "
    SELECT qq.questionID, qq.question, qq.question_type, qq.correct_answer,
           qc.choiceID, qc.option_label, qc.option_text,
           aa.assessment_time
    FROM quiz_questions qq
    LEFT JOIN quiz_choices qc ON qq.questionID = qc.questionID
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
    $qid = $row['questionID'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'questionID' => $row['questionID'],
            'question' => $row['question'],
            'type' => $row['question_type'],
            'correct' => $row['correct_answer'],
            'choices' => []
        ];
    }
    if ($row['choiceID']) {
        $questions[$qid]['choices'][] = [
            'choiceID' => $row['choiceID'],
            'label' => $row['option_label'],
            'text' => $row['option_text']
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
<h1>Take Quiz</h1>
<div id="timer" style="font-size:20px; font-weight:bold; color:red;"></div>
<form id="quizForm" action="../action/submitQuiz.php" method="POST">
    <input type="hidden" name="quizID" value="<?= htmlspecialchars($quizID) ?>">
    <div id="question-container"></div>
    <div style="margin-top:20px;">
        <button type="button" id="prevBtn" disabled>Previous</button>
        <button type="button" id="nextBtn">Next</button>
        <button type="submit" id="submitBtn" style="display:none;">Submit Quiz</button>
    </div>
</form>

<script>
const quizID = <?= (int)$quizID ?>;
const questions = <?= json_encode(array_values($questions)) ?>;

let currentIndex = parseInt(localStorage.getItem(`quiz_${quizID}_currentIndex`)) || 0;
let savedAnswers = JSON.parse(localStorage.getItem(`quiz_${quizID}_answers`)) || {};
let tabSwitchCount = 0;
let warnedOnce = false;
let isSubmitting = false;
let submissionSent = false;

const container = document.getElementById('question-container');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const submitBtn = document.getElementById('submitBtn');
const quizForm = document.getElementById('quizForm');

// Render question
function renderQuestion(index) {
    const q = questions[index];
    const progress = ((index + 1) / questions.length) * 100;
    let html = `
        <p><strong>Question ${index + 1} of ${questions.length}</strong></p>
        <div class="progress-container">
            <div class="progress-bar" style="width:${progress}%;"></div>
        </div>
        <p>${q.question}</p>
    `;
    const savedAnswer = savedAnswers[q.questionID] || "";

    if (q.type === 'multiple') {
        q.choices.forEach(c => {
            const isChecked = savedAnswer == c.choiceID ? 'checked' : '';
            html += `<label>
                        <input type="radio" name="answer[${q.questionID}]" value="${c.choiceID}" ${isChecked}>
                        ${c.label}. ${c.text}
                    </label><br>`;
        });
    } else if (q.type === 'truefalse') {
        html += `<label><input type="radio" name="answer[${q.questionID}]" value="True" ${savedAnswer === "True" ? "checked" : ""}> True</label><br>`;
        html += `<label><input type="radio" name="answer[${q.questionID}]" value="False" ${savedAnswer === "False" ? "checked" : ""}> False</label>`;
    } else if (q.type === 'identification') {
        html += `<input type="text" name="answer[${q.questionID}]" value="${savedAnswer}">`;
    }

    container.innerHTML = html;
    prevBtn.disabled = index === 0;
    nextBtn.style.display = index === questions.length - 1 ? 'none' : 'inline-block';
    submitBtn.style.display = index === questions.length - 1 ? 'inline-block' : 'none';

    localStorage.setItem(`quiz_${quizID}_currentIndex`, index);

    document.querySelectorAll(`[name="answer[${q.questionID}]"]`).forEach(input => {
        input.addEventListener('change', () => {
            savedAnswers[q.questionID] = input.type === "radio" ? input.value : input.value.trim();
            localStorage.setItem(`quiz_${quizID}_answers`, JSON.stringify(savedAnswers));
        });
    });
}

prevBtn.addEventListener('click', () => {
    if (currentIndex > 0) {
        currentIndex--;
        renderQuestion(currentIndex);
    }
});

nextBtn.addEventListener('click', () => {
    if (currentIndex < questions.length - 1) {
        currentIndex++;
        renderQuestion(currentIndex);
    }
});

renderQuestion(currentIndex);

// Timer
let duration = <?= $duration ?>;
const timerElem = document.getElementById('timer');
function updateTimer() {
    let minutes = Math.floor(duration / 60);
    let seconds = duration % 60;
    timerElem.textContent = `${minutes}:${seconds.toString().padStart(2,'0')}`;
    if (duration <= 0 && !isSubmitting && !submissionSent) {
        triggerSubmission();
    }
    duration--;
}
setInterval(updateTimer, 1000);

// Back button detection
history.pushState(null, null, location.href);
window.onpopstate = function () {
    if (!isSubmitting && !submissionSent) {
        if (confirm("Are you sure you want to go back? Your quiz will automatically be submitted.")) {
            triggerSubmission();
        } else {
            history.pushState(null, null, location.href);
        }
    }
};

// Tab switch detection
document.addEventListener("visibilitychange", function () {
    if (isSubmitting || submissionSent) return;
    if (document.visibilityState === "hidden") {
        if (!warnedOnce) {
            warnedOnce = true;
            alert("⚠ Tab switching will be detected during the quiz. Further tab switches will be recorded.");
        } else {
            tabSwitchCount++;
        }
    }
});

// Before unload (sendBeacon auto-submit)
function beforeUnloadHandler(e) {
    if (isSubmitting || submissionSent) return;
    if (Object.keys(savedAnswers).length > 0) {
        sendBeaconSubmit();
    }
    e.preventDefault();
    e.returnValue = "If you leave, your quiz will be submitted automatically.";
}
window.addEventListener("beforeunload", beforeUnloadHandler);

// Normal submit
quizForm.addEventListener("submit", function (e) {
    e.preventDefault();
    if (confirm("Are you sure you want to submit your quiz?")) {
        triggerSubmission();
    }
});

// Unified submission logic
function triggerSubmission() {
    submissionSent = true;
    isSubmitting = true;
    window.removeEventListener("beforeunload", beforeUnloadHandler);
    sendFormSubmit();
}

function sendFormSubmit() {
    clearLocalData();
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "../action/submitQuiz.php";

    form.innerHTML = `
        <input type="hidden" name="quizID" value="${quizID}">
        <input type="hidden" name="tab_switch_count" value="${tabSwitchCount}">
        <input type="hidden" name="answer" value='${JSON.stringify(savedAnswers)}'>
    `;

    document.body.appendChild(form);
    form.submit();
}

function sendBeaconSubmit() {
    clearLocalData();
    const payload = new FormData();
    payload.append('quizID', quizID);
    payload.append('tab_switch_count', tabSwitchCount);
    payload.append('answer', JSON.stringify(savedAnswers));
    navigator.sendBeacon('../action/submitQuiz.php', payload);
}

// Always clear localStorage before redirect
function clearLocalData() {
    localStorage.removeItem(`quiz_${quizID}_answers`);
    localStorage.removeItem(`quiz_${quizID}_currentIndex`);
    // Redirect to results page after submission
    setTimeout(() => {
        window.location.href = '../student/student-subject-landingpage.php';
    }, 500);
}
</script>
</body>
</html>