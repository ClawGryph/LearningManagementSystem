// ================== QUIZ STATE ==================
let currentIndex = parseInt(localStorage.getItem(`quiz_${quizID}_currentIndex`)) || 0;
let savedAnswers = JSON.parse(localStorage.getItem(`quiz_${quizID}_answers`)) || {};
let tabSwitchCount = parseInt(localStorage.getItem(`quiz_${quizID}_tabSwitchCount`)) || 0;
let warnedOnce = localStorage.getItem(`quiz_${quizID}_warnedOnce`) === "true";

let isSubmitting = false;
let submissionSent = false;
let isClosingOrUnloading = false;

// DOM references
const container = document.getElementById("question-container");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");
const submitBtn = document.getElementById("submitBtn");
const quizForm = document.getElementById("quizForm");
const timerElem = document.getElementById("timer");

// ================== RENDER QUESTION ==================
function renderQuestion(index) {
    const q = questions[index];
    const progress = ((index + 1) / questions.length) * 100;
    const savedAnswer = savedAnswers[q.questionID] || "";

    let html = `
        <p><strong>Question ${index + 1} / ${questions.length}</strong></p>
        <div class="progress-container">
            <div class="progress-bar" style="width:${progress}%;"></div>
        </div>
        <p class="question"><span>Q:</span> ${q.question}</p>
    `;

    if (q.type === "multiple") {
        q.choices.forEach(c => {
            const isChecked = savedAnswer == c.choiceID ? "checked" : "";
            html += `
                <label>
                    <input type="radio" name="answer[${q.questionID}]" value="${c.choiceID}" ${isChecked}>
                    ${c.label}. ${c.text}
                </label><br>
            `;
        });
    } else if (q.type === "truefalse") {
        html += `
            <label><input type="radio" name="answer[${q.questionID}]" value="True" ${savedAnswer === "True" ? "checked" : ""}> True</label><br>
            <label><input type="radio" name="answer[${q.questionID}]" value="False" ${savedAnswer === "False" ? "checked" : ""}> False</label>
        `;
    } else if (q.type === "identification") {
        html += `<input type="text" name="answer[${q.questionID}]" value="${savedAnswer}">`;
    }

    container.innerHTML = html;

    // Button states
    prevBtn.disabled = index === 0;
    nextBtn.style.display = index === questions.length - 1 ? "none" : "inline-block";
    submitBtn.style.display = index === questions.length - 1 ? "inline-block" : "none";

    localStorage.setItem(`quiz_${quizID}_currentIndex`, index);

    // Auto-save answers
    document.querySelectorAll(`[name="answer[${q.questionID}]"]`).forEach(input => {
        input.addEventListener(input.type === "text" ? "input" : "change", () => {
            savedAnswers[q.questionID] = input.type === "text" ? input.value.trim() : input.value;
            localStorage.setItem(`quiz_${quizID}_answers`, JSON.stringify(savedAnswers));
        });
    });
}

// ================== NAVIGATION ==================
function goToPrevious() {
    if (currentIndex > 0) {
        currentIndex--;
        renderQuestion(currentIndex);
    }
}

function goToNext() {
    if (currentIndex < questions.length - 1) {
        currentIndex++;
        renderQuestion(currentIndex);
    }
}

prevBtn.addEventListener("click", goToPrevious);
nextBtn.addEventListener("click", goToNext);
renderQuestion(currentIndex);

// ================== TIMER ==================
function updateTimer() {
    let minutes = Math.floor(duration / 60);
    let seconds = duration % 60;
    timerElem.textContent = `Time left: ${minutes}:${seconds.toString().padStart(2, "0")}`;

    if (duration <= 0 && !isSubmitting && !submissionSent) {
        triggerSubmission();
    }
    duration--;
}
setInterval(updateTimer, 1000);

// ================== BACK BUTTON PROTECTION ==================
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

// ================== TAB SWITCH DETECTION ==================
function handleTabOrBlur() {
    if (isSubmitting || submissionSent || isClosingOrUnloading) return;

    tabSwitchCount++;
    localStorage.setItem(`quiz_${quizID}_tabSwitchCount`, tabSwitchCount);

    if (!warnedOnce) {
        warnedOnce = true;
        alert("⚠ Tab switching or leaving the quiz window will be detected...");
        localStorage.setItem(`quiz_${quizID}_warnedOnce`, "true");
    }
}

// Detect when tab is hidden (switched)
document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "hidden") {
        handleTabOrBlur();
    }
});

// Detect when window loses focus (blur = clicked outside / switched app / dual screen)
window.addEventListener("blur", handleTabOrBlur);

// Prevent false alerts when closing
window.addEventListener("pagehide", () => {
    isClosingOrUnloading = true;
});

// ================== SYNC HELPERS ==================
function collectAnswers() {
    const allInputs = document.querySelectorAll("#quizForm input[name^='answer']");
    let tempAnswers = { ...savedAnswers };

    allInputs.forEach(input => {
        const match = input.name.match(/answer\[(\d+)\]/);
        if (!match) return;

        const qid = match[1];
        if (input.type === "radio" && input.checked) {
            tempAnswers[qid] = input.value;
        } else if (input.type === "text") {
            tempAnswers[qid] = input.value.trim();
        }
    });

    savedAnswers = tempAnswers;
    localStorage.setItem(`quiz_${quizID}_answers`, JSON.stringify(savedAnswers));
}

function forceSyncFromDOMAndStorage() {
    collectAnswers();
    tabSwitchCount = parseInt(localStorage.getItem(`quiz_${quizID}_tabSwitchCount`)) || 0;
    savedAnswers = JSON.parse(localStorage.getItem(`quiz_${quizID}_answers`)) || {};
}

// ================== SUBMISSION ==================
function triggerSubmission() {
    submissionSent = true;
    isSubmitting = true;
    window.removeEventListener("beforeunload", beforeUnloadHandler);
    sendFormSubmit();
}

quizForm.addEventListener("submit", e => {
    e.preventDefault();
    if (confirm("Are you sure you want to submit your quiz?")) {
        triggerSubmission();
    }
});

// Auto-submit on unload
function beforeUnloadHandler() {
    if (isSubmitting || submissionSent) return;
    isClosingOrUnloading = true;
    collectAnswers();
    sendBeaconSubmit();
}
window.addEventListener("beforeunload", beforeUnloadHandler);

// ================== SUBMIT HELPERS ==================
function sendFormSubmit() {
    collectAnswers();

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "../action/submitQuiz.php";

    const addField = (name, value) => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        input.value = value;
        form.appendChild(input);
    };

    addField("quizID", quizID);
    addField("tab_switch_count", localStorage.getItem(`quiz_${quizID}_tabSwitchCount`) || 0);
    addField("submit_type", "manual");
    addField("answer", JSON.stringify(savedAnswers));

    document.body.appendChild(form);
    form.submit();
}

function sendBeaconSubmit() {
    collectAnswers();

    const payload = new FormData();
    payload.append("quizID", quizID);
    payload.append("tab_switch_count", localStorage.getItem(`quiz_${quizID}_tabSwitchCount`) || 0);
    payload.append("submit_type", "auto");
    payload.append("answer", JSON.stringify(savedAnswers));

    navigator.sendBeacon("../action/submitQuiz.php", payload);
}

// ================== CLEANUP ==================
function clearLocalData() {
    ["answers", "currentIndex", "tabSwitchCount", "warnedOnce"].forEach(key => {
        localStorage.removeItem(`quiz_${quizID}_${key}`);
    });

    setTimeout(() => {
        window.location.href = "../student/student-subject-landingpage.php";
    }, 500);
}