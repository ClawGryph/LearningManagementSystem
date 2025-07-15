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

        // Reset values
        newBlock.querySelectorAll("input").forEach(input => input.value = "");
        newBlock.querySelectorAll("select").forEach(select => select.selectedIndex = 0);

        // Show default (multiple choice), hide others
        newBlock.querySelector(".multipleChoiceInputs").style.display = "block";
        newBlock.querySelectorAll(".multipleChoiceInputs input").forEach(el => el.required = true);

        newBlock.querySelector(".identificationInputs").style.display = "none";
        newBlock.querySelectorAll(".identificationInputs input").forEach(el => el.required = false);

        newBlock.querySelector(".trueFalseInputs").style.display = "none";
        newBlock.querySelectorAll(".trueFalseInputs input, .trueFalseInputs select").forEach(el => el.required = false);

        // Update the Question # number
        const span = newBlock.querySelector(".questionNumber");
        if (span) span.textContent = questionCount;

        container.appendChild(newBlock);

        // Re-bind dropdown for the new block
        bindDropdownChange(newBlock.querySelector(".quizTypeSelect"));
    });

    // Dropdown logic
    function bindDropdownChange(selectElement) {
        selectElement.addEventListener("change", function () {
            const block = selectElement.closest(".question-block");

            const multipleInputs = block.querySelector(".multipleChoiceInputs");
            const identificationInputs = block.querySelector(".identificationInputs");
            const trueFalseInputs = block.querySelector(".trueFalseInputs");

            // Hide all and remove required
            [multipleInputs, identificationInputs, trueFalseInputs].forEach(section => {
                section.style.display = "none";
                section.querySelectorAll("input, select").forEach(el => {
                    el.required = false;
                });
            });

            // Show selected and set required
            if (this.value === "multiple") {
                multipleInputs.style.display = "block";
                multipleInputs.querySelectorAll("input").forEach(el => el.required = true);
            } else if (this.value === "identification") {
                identificationInputs.style.display = "block";
                identificationInputs.querySelectorAll("input").forEach(el => el.required = true);
            } else if (this.value === "truefalse") {
                trueFalseInputs.style.display = "block";
                trueFalseInputs.querySelectorAll("input, select").forEach(el => el.required = true);
            }
        });
    }

    // Final validation before submit
    document.getElementById("quizForm").addEventListener("submit", function (e) {
        const form = this;

        // Ensure correct fields are required before validation
        document.querySelectorAll(".question-block").forEach(block => {
            const type = block.querySelector(".quizTypeSelect").value;

            // Reset all required
            block.querySelectorAll("input, select").forEach(el => el.required = false);

            // Set required based on selected type
            if (type === "multiple") {
                block.querySelectorAll(".multipleChoiceInputs input").forEach(el => el.required = true);
            } else if (type === "identification") {
                block.querySelectorAll(".identificationInputs input").forEach(el => el.required = true);
            } else if (type === "truefalse") {
                block.querySelectorAll(".trueFalseInputs input, .trueFalseInputs select").forEach(el => el.required = true);
            }
        });

        // Check validity
        if (!form.checkValidity()) {
            e.preventDefault();
            form.reportValidity(); // Let browser show messages
        }
        // else: let the form submit
    });
});