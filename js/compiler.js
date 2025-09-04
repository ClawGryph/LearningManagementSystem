document.addEventListener("DOMContentLoaded", () => {
    const runBtn = document.getElementById("runBtn");
    const codeInput = document.getElementById("compilerInput");
    const output = document.getElementById("output");
    const compiler = document.querySelector(".compiler");
    const toggleBtn = document.getElementById("toggleFullscreen");
    const timerElement = document.getElementById("timer");
    const form = document.getElementById("compilerForm");

    // Timer setup
    let remaining = (Number(assessmentTimeMinutes) || 0) * 60; // seconds
    let timerInterval = null;
    let isSubmitting = false;

    // 🔹 Tab Switch Counter
    let tabSwitchCount = 0;
    let firstWarningShown = false;

    function handleBlur() {
        if (isSubmitting) return; // ignore if submitting
        if (!firstWarningShown) {
            alert("⚠️ You have switched tabs. Please focus on the activity.");
            firstWarningShown = true;
        } else {
            tabSwitchCount++;
        }
    }

    window.addEventListener("blur", handleBlur);

    // 🔹 Centralized submission logic
    function submitForm(auto = false) {
        if (isSubmitting) return;
        isSubmitting = true;

        clearInterval(timerInterval);
        window.removeEventListener("blur", handleBlur);

        // Ensure textarea value is synced
        // document.querySelector("textarea[name='code_submission']").value = codeInput.value;

        const formData = new FormData(form);
        formData.append("tabSwitchCount", tabSwitchCount);

        // Debug log
        // console.log(auto ? "Auto-submitting due to timer expiry..." : "Manual submit");
        // console.log("FormData dump:", [...formData.entries()]);

        fetch("../action/submitActivity.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            console.log("Server response:", data);

            if (data.status === "success") {
                alert("✅ " + data.message);
                window.location.href = '../student/student-subject-landingpage.php';
            } else {
                alert("❌ " + data.message);
                isSubmitting = false; // allow retry if failed
                timerInterval = setInterval(updateTimer, 1000); // resume timer if needed
                window.addEventListener("blur", handleBlur); // reattach listener
            }
        })
        .catch(err => {
            console.error("Fetch error:", err);
            alert("❌ Submission failed. Please try again.");
            isSubmitting = false;
            timerInterval = setInterval(updateTimer, 1000);
            window.addEventListener("blur", handleBlur);
        });

        // fetch("../action/submitActivity.php", {
        //     method: "POST",
        //     body: formData
        // })
        // .then(res => res.text()) // 👈 TEMP: get raw text
        // .then(txt => {
        //     console.log("Raw server response:", txt);
        //     try {
        //         const data = JSON.parse(txt);
        //         console.log("Parsed JSON:", data);
        //     } catch (e) {
        //         console.error("JSON parse error:", e);
        //         alert("Server did not return valid JSON. Check console.");
        //     }
        // })
        // .catch(err => {
        //     console.error("Fetch error:", err);
        // });

    }

    // Timer countdown
    function updateTimer() {
        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        timerElement.textContent = `⏳ ${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;

        if (remaining > 0) {
            remaining--;
        } else {
            clearInterval(timerInterval);
            if (!isSubmitting) {
                alert("⏰ Time is up! Submitting your code...");
                submitForm(true); // ✅ Auto-submit directly
            }
        }
    }

    if (remaining > 0) {
        timerInterval = setInterval(updateTimer, 1000);
    }

    // Run Code
    runBtn.addEventListener("click", () => {
        const code = codeInput.value;
        const language = activityLanguage;

        output.innerText = "⏳ Running your code...";

        fetch("../action/proxy.php", {
            method: "POST",
            headers: {
                "content-type": "application/json"
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

    // Manual submission handler
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        submitForm(false);
    });
});

// Map languages
function getLanguageID(language) {
    switch (language.toLowerCase()) {
        case "c": return 50;
        case "java": return 62;
        default: return 50; // fallback
    }
}