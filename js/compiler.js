document.addEventListener("DOMContentLoaded", () => {
    const runBtn = document.getElementById("runBtn");
    const codeInput = document.getElementById("compilerInput");
    const output = document.getElementById("output");
    const compiler = document.querySelector(".compiler");
    const toggleBtn = document.getElementById("toggleFullscreen");
    const timerElement = document.getElementById("timer");

    // Timer setup
    let remaining = (Number(assessmentTimeMinutes) || 0) * 60; // seconds

    function updateTimer() {
        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        timerElement.textContent = `⏳ ${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;

        if (remaining > 0) {
            remaining--;
            setTimeout(updateTimer, 1000);
        } else {
            alert("⏰ Time is up! Submitting your code...");
            document.getElementById("compilerForm").submit();
        }
    }

    if (remaining > 0) updateTimer();

    // Run Code
    runBtn.addEventListener("click", () => {
        const code = codeInput.value;
        const language = activityLanguage;

        output.innerText = "⏳ Running your code...";

        fetch("https://judge0-ce.p.rapidapi.com/submissions?base64_encoded=false&wait=true", {
            method: "POST",
            headers: {
                "content-type": "application/json",
                "X-RapidAPI-Host": "judge0-ce.p.rapidapi.com",
                "X-RapidAPI-Key": "9c2e724a78mshdfce4f8721eb7f8p12c000jsn6fce8755d692"
            },
            body: JSON.stringify({
                source_code: code,
                language_id: getLanguageID(language)
            })
        })
        .then(res => res.json())
        .then(data => {
            output.innerText = data.stdout || data.stderr || "⚠️ No output";
        })
        .catch(err => {
            output.innerText = "❌ Error: " + err;
        });
    });

    // Fullscreen Toggle
    toggleBtn.addEventListener("click", () => {
        compiler.classList.toggle("fullscreen");
        if (compiler.classList.contains("fullscreen")) {
            toggleBtn.innerHTML = '<i class="fa-solid fa-down-left-and-up-right-to-center"></i>';
            toggleBtn.title = "Exit Fullscreen";
        } else {
            toggleBtn.innerHTML = '<i class="fa-solid fa-up-right-and-down-left-from-center"></i>';
            toggleBtn.title = "Fullscreen";
        }
    });

    // 🔹 Tab Switch Counter
    let tabSwitchCount = 0;
    let firstWarningShown = false;
    let isSubmitting = false;

    window.addEventListener("blur", () => {
        if (isSubmitting) return; // 🔹 Ignore blur if form is submitting

        if (!firstWarningShown) {
            alert("⚠️ You have switched tabs. Please focus on the activity.");
            firstWarningShown = true;
        } else {
            tabSwitchCount++;
        }
    });

    // Handle Submission
    document.getElementById("compilerForm").addEventListener("submit", function(e) {
        isSubmitting = true;
        e.preventDefault();

        const formData = new FormData(this);
        formData.append("tabSwitchCount", tabSwitchCount);

        fetch("../action/submitActivity.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                alert("✅ " + data.message);
                window.location.href = '../student/student-subject-landingpage.php';
            } else {
                alert("❌ " + data.message);
            }
        })
        .catch(err => console.error(err));
    });
});

// Map languages
function getLanguageID(language) {
    switch(language.toLowerCase()) {
        case "c": return 50;
        case "java": return 62;
        default: return 50; // fallback
    }
}