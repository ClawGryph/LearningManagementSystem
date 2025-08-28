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
<div class="fullPageContainer">
    <div class="fullPageContent">
        <div class="fullPageHeader">
            <div id="quizTitle"><?= htmlspecialchars($quizTitle) ?></div>
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

let currentIndex = parseInt(localStorage.getItem(`quiz_${quizID}_currentIndex`)) || 0;
let savedAnswers = JSON.parse(localStorage.getItem(`quiz_${quizID}_answers`)) || {};
let tabSwitchCount = parseInt(localStorage.getItem(`quiz_${quizID}_tabSwitchCount`)) || 0;
let warnedOnce = localStorage.getItem(`quiz_${quizID}_warnedOnce`) === "true";
let isSubmitting = false;
let submissionSent = false;
let isClosingOrUnloading = false;

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
        <p><strong>Question ${index + 1} / ${questions.length}</strong></p>
        <div class="progress-container">
            <div class="progress-bar" style="width:${progress}%;"></div>
        </div>
        <p class="question"><span>Q:</span> ${q.question}</p>
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

    // ✅ Improved auto-save handling
    document.querySelectorAll(`[name="answer[${q.questionID}]"]`).forEach(input => {
        if (input.type === "text") {
            input.addEventListener('input', () => {   // save on typing
                savedAnswers[q.questionID] = input.value.trim();
                localStorage.setItem(`quiz_${quizID}_answers`, JSON.stringify(savedAnswers));
            });
        } else {
            input.addEventListener('change', () => {  // save on selection
                savedAnswers[q.questionID] = input.value;
                localStorage.setItem(`quiz_${quizID}_answers`, JSON.stringify(savedAnswers));
            });
        }
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

// ✅ Always persist answers on every input
quizForm.addEventListener("input", () => {
    collectAnswers();
});

// Timer
let duration = <?= $duration ?>;
const timerElem = document.getElementById('timer');
function updateTimer() {
    let minutes = Math.floor(duration / 60);
    let seconds = duration % 60;
    timerElem.textContent = `Time left: ${minutes}:${seconds.toString().padStart(2,'0')}`;
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
    if (isSubmitting || submissionSent || isClosingOrUnloading) return;
    if (document.visibilityState === "hidden") {
        tabSwitchCount++;
        localStorage.setItem(`quiz_${quizID}_tabSwitchCount`, tabSwitchCount);
        console.log(`Tab switched ${tabSwitchCount} times`);

        if (!warnedOnce) {
            warnedOnce = true;
            alert("⚠ Tab switching will be detected...");
            localStorage.setItem(`quiz_${quizID}_warnedOnce`, "true");
        }
    }
});

// Mark unloading to stop false alerts
window.addEventListener("pagehide", () => {
    isClosingOrUnloading = true;
});

// ✅ New: unified sync from DOM + storage
function forceSyncFromDOMAndStorage() {
    collectAnswers();
    tabSwitchCount = parseInt(localStorage.getItem(`quiz_${quizID}_tabSwitchCount`)) || 0;
    savedAnswers = JSON.parse(localStorage.getItem(`quiz_${quizID}_answers`)) || {};
}

// Before unload (sendBeacon auto-submit)
function beforeUnloadHandler(e) {
    if (isSubmitting || submissionSent) return;
    isClosingOrUnloading = true;
    collectAnswers();
    sendBeaconSubmit();   // auto
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
    sendFormSubmit();   // manual
}

// Collect answers from current DOM
function collectAnswers() {
    const allInputs = document.querySelectorAll("#quizForm input[name^='answer']");
    let tempAnswers = { ...savedAnswers };  // keep old answers

    allInputs.forEach(input => {
        const qidMatch = input.name.match(/answer\[(\d+)\]/);
        if (qidMatch) {
            const qid = qidMatch[1];
            if (input.type === "radio" && input.checked) {
                tempAnswers[qid] = input.value;
            } else if (input.type === "text") {
                tempAnswers[qid] = input.value.trim();
            }
        }
    });

    savedAnswers = tempAnswers;
    localStorage.setItem(`quiz_${quizID}_answers`, JSON.stringify(savedAnswers));

    console.log("Saved answers now:", savedAnswers);
}

function sendFormSubmit() {
    collectAnswers();  // ✅ force fresh DOM read
    const latestAnswers = savedAnswers;
    const latestTabCount = parseInt(localStorage.getItem(`quiz_${quizID}_tabSwitchCount`) || '0');

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "../action/submitQuiz.php";

    const safeAnswers = JSON.stringify(latestAnswers);

    form.innerHTML = `
        <input type="hidden" name="quizID" value="${quizID}">
        <input type="hidden" name="tab_switch_count" value="${latestTabCount}">
        <input type="hidden" name="submit_type" value="manual">
        <input type="hidden" name="answer" value='${safeAnswers}'>
    `;

    document.body.appendChild(form);
    form.submit();
}

function sendBeaconSubmit() {
    collectAnswers();  // ✅ force fresh DOM read
    const latestAnswers = savedAnswers;
    const latestTabCount = parseInt(localStorage.getItem(`quiz_${quizID}_tabSwitchCount`) || '0');

    const payload = new FormData();
    payload.append('quizID', quizID);
    payload.append('tab_switch_count', latestTabCount);
    payload.append('submit_type', 'auto');
    payload.append('answer', JSON.stringify(latestAnswers));

    navigator.sendBeacon('../action/submitQuiz.php', payload);
}

// Always clear localStorage before redirect
function clearLocalData() {
    localStorage.removeItem(`quiz_${quizID}_answers`);
    localStorage.removeItem(`quiz_${quizID}_currentIndex`);
    localStorage.removeItem(`quiz_${quizID}_tabSwitchCount`);
    localStorage.removeItem(`quiz_${quizID}_warnedOnce`);
    setTimeout(() => {
        window.location.href = '../student/student-subject-landingpage.php';
    }, 500);
}
</script>

</body>
</html>