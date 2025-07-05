document.addEventListener("DOMContentLoaded", function () {
    const toggleIcons = document.querySelectorAll(".toggle-password");

    toggleIcons.forEach(icon => {
        icon.addEventListener("click", function () {
            const targetInput = document.querySelector(this.getAttribute("toggle"));
            const type = targetInput.getAttribute("type") === "password" ? "text" : "password";
            targetInput.setAttribute("type", type);

            // Toggle eye icon class
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    });
});