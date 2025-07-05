document.addEventListener("DOMContentLoaded", function () {
    const signupForm = document.getElementById("signup");
    const passwordInput = document.getElementById("signup-password");
    const confirmPassInput = document.getElementById("confirmPass");
    const errorMessage = signupForm.querySelector(".error-message");

    function isStrongPassword(password) {
        // Regex: min 8 chars, uppercase, lowercase, digit, special char
        const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        return strongRegex.test(password);
    }

    signupForm.addEventListener("submit", function (e) {
        const password = passwordInput.value;
        const confirmPass = confirmPassInput.value;

        if (password !== confirmPass) {
            e.preventDefault(); // Stop form submission
            errorMessage.textContent = "Passwords do not match.";
            return;
        }

        if (!isStrongPassword(password)) {
            e.preventDefault(); // Stop form submission
            errorMessage.textContent = "Password: min. 8 chars, with uppercase, lowercase, number & symbol.";
            return;
        }

    
        // Clear message if valid
        errorMessage.textContent = "";
    });
});