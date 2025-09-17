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
  const progress2RoleBtns = progress2.querySelectorAll(".roleBtn");
  const progress2Inputs = progress2.querySelectorAll("input");
  const progress2Btn = document.querySelector(".progress2-btns");
  const submitBtn = document.querySelector(".progress2-btns .submit-btn");
  const agreePrivacyCheckbox = document.getElementById("agreePrivacy");

//   PROGRESS 3
const progress3 = document.querySelector(".progress3");
const progress3Inputs = document.getElementById("emailCode");
const progress3Btn = document.querySelector(".progress3-btns");
const verifyBtn = document.querySelector(".progress3-btns .verify-btn");

// Headers
  const headerProgress2 = document.querySelector(".form_2_progress");
  const headerProgress3 = document.querySelector(".form_3_progress");

  let selectedRole = null; // track role
  const loadingOverlay = document.getElementById("loadingOverlay");

  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("signup-password");
  const confirmPassInput = document.getElementById("confirmPass");

  nextBtn.disabled = true; // default
  submitBtn.disabled = true; // default

  // Validators
  const isValidEmail = email =>
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

  const isStrongPassword = password =>
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/.test(password);

  const doPasswordsMatch = () =>
    passwordInput.value === confirmPassInput.value && passwordInput.value.trim() !== "";

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

  // Validation logic for step 1
  function validateFormStep1() {
    let allValid = true;

    if (!isValidEmail(emailInput.value)) {
      showError(emailInput, "Invalid email format");
      allValid = false;
    } else clearError(emailInput);

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

  // Enable Next button only if all fields are filled
  function checkFilledFieldsStep1() {
    const allFilled = Array.from(progress1Inputs).every(input => input.value.trim() !== "");
    nextBtn.disabled = !allFilled;
  }

  progress1Inputs.forEach(input => {
    input.addEventListener("input", checkFilledFieldsStep1);
  });

  // Step 2 check: role + inputs
  function checkStep2() {
    const allFilled = Array.from(progress2Inputs).every(input => input.value.trim() !== "");
    const privacyChecked = agreePrivacyCheckbox.checked;

    submitBtn.disabled = !(selectedRole && allFilled && privacyChecked);
  }

  agreePrivacyCheckbox.addEventListener("change", checkStep2);

  progress2Inputs.forEach(input => {
    input.addEventListener("input", checkStep2);
  });

  // Role button selection
  progress2RoleBtns.forEach(btn => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      // clear previous active
      progress2RoleBtns.forEach(b => b.classList.remove("active"));

      // mark this as active
      this.classList.add("active");

      // save selected role
      selectedRole = this.textContent.trim();
      document.getElementById("roleInput").value = selectedRole;

      checkStep2(); // re-check when role chosen
    });
  });

 

  // Only validate on Next button click
  nextBtn.addEventListener("click", function (e) {
    e.preventDefault();
    if (validateFormStep1()) {
      progress1.style.display = "none";
      progress2.style.display = "block";

      nextBtn.style.display = "none";
      progress2Btn.style.display = "flex";
      progress2Btn.style.justifyContent = "center";
      progress2Btn.style.alignItems = "flex-start";

      headerProgress2.classList.add("active");
    }
  });


  submitBtn.addEventListener("click", function(e) {
    e.preventDefault();

    const formData = new FormData(document.getElementById("signup"));

    loadingOverlay.classList.add('active');

    fetch("./action/preSignup.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if (data.status === "error") {
          loadingOverlay.classList.remove('active');
            alert(data.message);
        } else {
          loadingOverlay.classList.add('active');

            setTimeout(() => {
                loadingOverlay.classList.remove('active');

                progress2.style.display = "none";
                progress2Btn.style.display = "none";
                
                progress3.style.display = "block";
                progress3Btn.style.display = "flex";
                progress3Btn.style.justifyContent = "center";
                progress3Btn.style.alignItems = "flex-start";

                headerProgress3.classList.add("active");
            }, 1000);
        }
    })
    .catch(err => {
        loadingOverlay.classList.remove('active');
        console.error(err);
        alert("Something went wrong. Please try again.");
    });
  });

    verifyBtn.addEventListener("click", function(e) {
        e.preventDefault();

        const formData = new FormData(document.getElementById("signup"));

        fetch("./action/signup.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "error") {
                showError(progress3Inputs, data.message);
            } else {
                clearError(progress3Inputs);
                progress3.style.display = "none";
                progress3Btn.style.display = "none";
                alert(data.message);
                window.location.href = "./index.php";
            }
        })
        .catch(err => console.error(err));
    });

});