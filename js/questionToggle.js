document.addEventListener("DOMContentLoaded", function () {
    let questionCount = 1;
    document.querySelector(".questionNumber").textContent = questionCount;

    const addBtn = document.getElementById("addQuestionBtn");
    const container = document.getElementById("questionsContainer");

    // Bind dropdown for the first block
    bindDropdownChange(document.querySelector(".quizTypeSelect"));

    addBtn.addEventListener("click", function () {
        const firstBlock = container.querySelector(".question-block");
        const newBlock = firstBlock.cloneNode(true);
        questionCount++;

        // Reset input values
        newBlock.querySelectorAll("input").forEach(input => input.value = "");
        newBlock.querySelectorAll("select").forEach(select => select.selectedIndex = 0);

        // Show default (multiple choice) and hide others
        newBlock.querySelector(".multipleChoiceInputs").style.display = "block";
        newBlock.querySelector(".identificationInputs").style.display = "none";
        newBlock.querySelector(".trueFalseInputs").style.display = "none";

        // Update the Question # number
        const span = newBlock.querySelector(".questionNumber");
        if (span) span.textContent = questionCount;

        container.appendChild(newBlock);

        // Re-bind dropdown for the new question block
        bindDropdownChange(newBlock.querySelector(".quizTypeSelect"));
    });

    // Function to handle dropdown switching
    function bindDropdownChange(selectElement) {
        selectElement.addEventListener("change", function () {
            const block = selectElement.closest(".question-block");
            const multipleInputs = block.querySelector(".multipleChoiceInputs");
            const identificationInputs = block.querySelector(".identificationInputs");
            const trueFalseInputs = block.querySelector(".trueFalseInputs");

            // Hide all
            multipleInputs.style.display = "none";
            identificationInputs.style.display = "none";
            trueFalseInputs.style.display = "none";

            // Show selected one
            if (this.value === "multiple") {
                multipleInputs.style.display = "block";
            } else if (this.value === "identification") {
                identificationInputs.style.display = "block";
            } else if (this.value === "truefalse") {
                trueFalseInputs.style.display = "block";
            }
        });
    }
});