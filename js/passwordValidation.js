document.addEventListener("DOMContentLoaded", function () {
    const toggleIcons = document.querySelectorAll(".toggle-password");

    //Toggle show/hide password
    toggleIcons.forEach(icon => {
        icon.addEventListener("click", function () {
        const targetInput = document.querySelector(this.dataset.toggle);
        targetInput.type = targetInput.type === "password" ? "text" : "password";

        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
        });
    });


    // PROGRESS 1
    const progress1 = document.querySelector(".progress1");
    const progress1Inputs = progress1.querySelectorAll("input");
    const nextBtn = document.querySelector(".progress1-btns .next-btn");

    // PROGRESS 2
    const progress2 = document.querySelector(".progress2");
    const progress2Inputs = progress2.querySelectorAll("input");
    const progress2Btn = document.querySelector(".progress2-btns");
    const verifyBtn = document.querySelector(".progress2-btns .verify-btn");

    // PROGRESS 3
    const progress3 = document.querySelector(".progress3");
    const progress3Inputs = progress3.querySelectorAll("input");
    const progress3Btn = document.querySelector(".progress3-btns");
    const resetBtn = document.querySelector(".progress3-btns .reset-btn");

    // HEADER
    const headerProgress2 = document.querySelector(".form_2_progress");
    const headerProgress3 = document.querySelector(".form_3_progress");

    const loadingOverlay = document.getElementById("loadingOverlay");

    nextBtn.disabled = true;
    if (verifyBtn) verifyBtn.disabled = true;
    if (resetBtn)  resetBtn.disabled  = true;

     // Error handling
    function showError(input, message) {
        let errorEl = input.parentElement.querySelector(".error-message");
        let inputField = input.parentElement;
        if (!errorEl) {
        errorEl = document.createElement("span");
        errorEl.classList.add("error-message");
        input.parentElement.appendChild(errorEl);

        inputField.classList.add("error");
        }
        errorEl.textContent = message;
    }

    function clearError(input) {
        const errorEl = input.parentElement.querySelector(".error-message");
        if (errorEl) errorEl.textContent = "";
    }

    // CHECK IF PROGRESS 1 INPUT IS FILLED
    function normalizeInputs(inputs) {
        return inputs && inputs.length !== undefined
            ? Array.from(inputs)
            : [inputs];
    }

    const inputs = normalizeInputs(progress1Inputs);

    function checkFilledFieldsStep1() {
        const allFilled = inputs.every(input => input.value.trim() !== "");
        nextBtn.disabled = !allFilled;
    }

    inputs.forEach(input => {
        input.addEventListener("input", checkFilledFieldsStep1);
    });

    //CHECK IF EMAIL IS PRESENT
    nextBtn.addEventListener("click", function (e) {
        e.preventDefault(); // Prevent form auto-submit
        
        const emailInput = document.getElementById('email');
        const emailValue = emailInput.value.trim();

        // Show loading while checking
        loadingOverlay.classList.add('active');

        fetch("action/checkEmail.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "email=" + encodeURIComponent(emailValue)
        })
        .then(res => res.json())
        .then(data => {
            if (data.exists) {
                // Email exists → go to step 2
                progress1.style.display = "none";
                nextBtn.style.display = "none";
                progress2.style.display = "block";
                progress2Btn.style.display = "flex";
                headerProgress2.classList.add("active");
            } else {
                // Email not found → show error
                showError(emailInput, "Email not found in our records.");
            }
        })
        .catch(err => {
            console.error("Error checking email:", err);
            showError(emailInput, "Something went wrong. Try again.");
        })
        .finally(() => {
            loadingOverlay.classList.remove('active');
        });
    });

    // PROGRESS 2
    const inputs2 = normalizeInputs(progress2Inputs);

    function checkFilledFieldsStep2(){
        const allFilled = inputs2.every(input => input.value.trim() !== "");
        verifyBtn.disabled = !allFilled;
    }

    inputs2.forEach(input => {
        input.addEventListener("input", checkFilledFieldsStep2);
    });

    // CHECK VERIFICATION CODE
    verifyBtn.addEventListener("click", function (e) {
        e.preventDefault();

        const codeInput = document.getElementById("emailCode");
        const codeValue = codeInput.value.trim();

        fetch("action/verifyCode.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "code=" + encodeURIComponent(codeValue)
        })
        .then(res => res.json())
        .then(data => {

            if (data.status === "success") {
                // Go to Step 3 (reset password)
                progress2.style.display = "none";
                progress2Btn.style.display = "none";
                progress3.style.display = "block";
                progress3Btn.style.display = "flex";
                headerProgress3.classList.add("active");
            } else {
                // Show error message
                showError(codeInput, data.message);
            }
        })
        .catch(err => {
            console.error("Error verifying code:", err);
            showError(codeInput, "Something went wrong. Try again.");
        });
    });


    // PROGRESS 3
    const passwordInput = document.getElementById("signup-password");
    const confirmPassInput = document.getElementById("confirmPass");

    // PASSWORD VALIDATORS
    const isStrongPassword = password =>
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/.test(password);

    const doPasswordsMatch = () =>
    passwordInput.value === confirmPassInput.value && passwordInput.value.trim() !== "";

    const inputs3 = normalizeInputs(progress3Inputs);

    function checkFilledFieldsStep3(){
        const allFilled = inputs3.every(input => input.value.trim() !== "");
        resetBtn.disabled = !allFilled;
    }

    inputs3.forEach(input => {
        input.addEventListener("input", checkFilledFieldsStep3);
    });

    function validateResetPasswordForm() {
        let allValid = true;

        if (!isStrongPassword(passwordInput.value)) {
        showError(passwordInput, "Must be 8+ chars: upper, lower, number, symbol");
        allValid = false;
        } else clearError(passwordInput);

        if (!doPasswordsMatch()) {
        showError(confirmPassInput, "Passwords do not match");
        allValid = false;
        } else clearError(confirmPassInput);

        return allValid;
    }

    resetBtn.addEventListener("click", function (e){
        e.preventDefault();

        if(validateResetPasswordForm()){
            const resetPass = passwordInput.value.trim();

            fetch("action/forgotPass.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "password=" + encodeURIComponent(resetPass)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    alert("Password updated successfully! You can now log in.");
                    // Redirect
                    window.location.href = "../index.php";
                } else {
                    showError(passwordInput, data.message);
                }
            })
            .catch(err => {
                console.error("Error resetting password:", err);
                showError(passwordInput, "Something went wrong. Try again.");
            });
        }
    });
});