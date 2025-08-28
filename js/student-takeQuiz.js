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

    // Auto-save
    document.querySelectorAll(`[name="answer[${q.questionID}]"]`).forEach(input => {
        if (input.type === "text") {
            input.addEventListener('input', () => {
                savedAnswers[q.questionID] = input.value.trim();
                localStorage.setItem(`quiz_${quizID}_answers`, JSON.stringify(savedAnswers));
            });
        } else {
            input.addEventListener('change', () => {
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

// Persist answers on every input
quizForm.addEventListener("input", () => {
    collectAnswers();
});

// Timer
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

        if (!warnedOnce) {
            warnedOnce = true;
            alert("⚠ Tab switching will be detected...");
            localStorage.setItem(`quiz_${quizID}_warnedOnce`, "true");
        }
    }
});

// Prevent false alerts
window.addEventListener("pagehide", () => {
    isClosingOrUnloading = true;
});

// Force sync from DOM + storage
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
    sendBeaconSubmit();
}
window.addEventListener("beforeunload", beforeUnloadHandler);

// Manual submit
quizForm.addEventListener("submit", function (e) {
    e.preventDefault();
    if (confirm("Are you sure you want to submit your quiz?")) {
        triggerSubmission();
    }
});

// Submission logic
function triggerSubmission() {
    submissionSent = true;
    isSubmitting = true;
    window.removeEventListener("beforeunload", beforeUnloadHandler);
    sendFormSubmit();
}

// Collect answers from DOM
function collectAnswers() {
    const allInputs = document.querySelectorAll("#quizForm input[name^='answer']");
    let tempAnswers = { ...savedAnswers };

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
}

// Manual form submit
function sendFormSubmit() {
    collectAnswers();
    const latestAnswers = savedAnswers;
    const latestTabCount = parseInt(localStorage.getItem(`quiz_${quizID}_tabSwitchCount`) || '0');

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "../action/submitQuiz.php";

    form.innerHTML = `
        <input type="hidden" name="quizID" value="${quizID}">
        <input type="hidden" name="tab_switch_count" value="${latestTabCount}">
        <input type="hidden" name="submit_type" value="manual">
        <input type="hidden" name="answer" value='${JSON.stringify(latestAnswers)}'>
    `;

    document.body.appendChild(form);
    form.submit();
}

// Auto-submit with Beacon
function sendBeaconSubmit() {
    collectAnswers();
    const latestAnswers = savedAnswers;
    const latestTabCount = parseInt(localStorage.getItem(`quiz_${quizID}_tabSwitchCount`) || '0');

    const payload = new FormData();
    payload.append('quizID', quizID);
    payload.append('tab_switch_count', latestTabCount);
    payload.append('submit_type', 'auto');
    payload.append('answer', JSON.stringify(latestAnswers));

    navigator.sendBeacon('../action/submitQuiz.php', payload);
}

// Clear storage
function clearLocalData() {
    localStorage.removeItem(`quiz_${quizID}_answers`);
    localStorage.removeItem(`quiz_${quizID}_currentIndex`);
    localStorage.removeItem(`quiz_${quizID}_tabSwitchCount`);
    localStorage.removeItem(`quiz_${quizID}_warnedOnce`);
    setTimeout(() => {
        window.location.href = '../student/student-subject-landingpage.php';
    }, 500);
}