const signinBtn = document.querySelector("#signin-btn");
const signupBtn = document.querySelector("#signup-btn");
const container = document.querySelector(".signin-signup-container");
const signinBtn2 = document.querySelector("#signin-btn2");
const signupBtn2 = document.querySelector("#signup-btn2");

signupBtn.addEventListener("click", () => {
    container.classList.add("signup-active");
});
signinBtn.addEventListener("click", () => {
    container.classList.remove("signup-active");
});
signupBtn2.addEventListener("click", () => {
    container.classList.add("signup-active2");
});
signinBtn2.addEventListener("click", () => {
    container.classList.remove("signup-active2");
});