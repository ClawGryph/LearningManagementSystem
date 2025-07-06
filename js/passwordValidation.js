document.addEventListener("DOMContentLoaded", function () {
    const signupForm = document.getElementById("signup");
    const forgotPassForm = document.getElementById("forgot-password");

    function isStrongPassword(password) {
        // Regex: min 8 chars, uppercase, lowercase, digit, special char
        const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        return strongRegex.test(password);
    }

    function validatePasswords(e) {
        const form = e.target;
        const passwordInput = form.querySelector("#signup-password");
        const confirmPassInput = form.querySelector("#confirmPass");
        const errorMessage = form.querySelector(".error-message");

        const password = passwordInput.value;
        const confirmPass = confirmPassInput.value;

        if (password !== confirmPass) {
            e.preventDefault();
            errorMessage.textContent = "Passwords do not match.";
            return false;
        }

        if (!isStrongPassword(password)) {
            e.preventDefault();
            errorMessage.textContent = "Password: min. 8 chars, with uppercase, lowercase, number & symbol.";
            return false;
        }

        errorMessage.textContent = "";
        return true;
    }

    if (signupForm) {
        signupForm.addEventListener("submit", validatePasswords);
    }

    if (forgotPassForm) {
        forgotPassForm.addEventListener("submit", validatePasswords);
    }
});