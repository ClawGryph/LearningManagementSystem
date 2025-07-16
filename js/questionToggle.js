function initQuestionToggle(){
    let questionCount = 1;
    const container = document.getElementById("questionsContainer");
    const addBtn = document.getElementById("addQuestionBtn");
    const initialSelect = document.querySelector(".quizTypeSelect");

    if (initialSelect) bindDropdownChange(initialSelect);

    if (addBtn) {
        addBtn.addEventListener("click", function () {
            const firstBlock = container.querySelector(".question-block");
            const newBlock = firstBlock.cloneNode(true);
            questionCount++;

            // Reset inputs
            newBlock.querySelectorAll("input").forEach(input => input.value = "");
            newBlock.querySelectorAll("select").forEach(select => select.selectedIndex = 0);

            // Show default multiple choice
            newBlock.querySelector(".multipleChoiceInputs").style.display = "block";
            newBlock.querySelectorAll(".multipleChoiceInputs input").forEach(el => el.required = true);

            newBlock.querySelector(".identificationInputs").style.display = "none";
            newBlock.querySelectorAll(".identificationInputs input").forEach(el => el.required = false);

            newBlock.querySelector(".trueFalseInputs").style.display = "none";
            newBlock.querySelectorAll(".trueFalseInputs input, .trueFalseInputs select").forEach(el => el.required = false);

            const span = newBlock.querySelector(".questionNumber");
            if (span) span.textContent = questionCount;

            container.appendChild(newBlock);

            bindDropdownChange(newBlock.querySelector(".quizTypeSelect"));
        });
    }

    function bindDropdownChange(selectElement) {
        selectElement.addEventListener("change", function () {
            const block = selectElement.closest(".question-block");

            const multipleInputs = block.querySelector(".multipleChoiceInputs");
            const identificationInputs = block.querySelector(".identificationInputs");
            const trueFalseInputs = block.querySelector(".trueFalseInputs");

            // Hide all and reset required
            [multipleInputs, identificationInputs, trueFalseInputs].forEach(section => {
                section.style.display = "none";
                section.querySelectorAll("input, select").forEach(el => {
                    el.required = false;
                });
            });

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

    // Form submit for new quiz
    const quizForm = document.getElementById("quizForm");
    if (quizForm) {
        quizForm.addEventListener("submit", function (e) {
            document.querySelectorAll(".question-block").forEach(block => {
                const type = block.querySelector(".quizTypeSelect").value;
                block.querySelectorAll("input, select").forEach(el => el.required = false);

                if (type === "multiple") {
                    block.querySelectorAll(".multipleChoiceInputs input").forEach(el => el.required = true);
                } else if (type === "identification") {
                    block.querySelectorAll(".identificationInputs input").forEach(el => el.required = true);
                } else if (type === "truefalse") {
                    block.querySelectorAll(".trueFalseInputs input, .trueFalseInputs select").forEach(el => el.required = true);
                }
            });

            if (!quizForm.checkValidity()) {
                e.preventDefault();
                quizForm.reportValidity();
            }
        });
    }

    // View Questions handler
    document.querySelectorAll(".view-questions").forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();

            const quizID = this.dataset.id;
            const container = document.getElementById("questionEditContainer");
            document.getElementById("editQuizID").value = quizID;

            fetch(`../action/fetchQuizQuestions.php?quizID=${quizID}`)
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;

                    container.querySelectorAll(".edit-question-block").forEach(block => {
                        const type = block.querySelector("input[name='questionType[]']").value;
                        const tfSelect = block.querySelector("select[name='correctAnswer[]']");
                        const mcInputs = block.querySelectorAll("input[name^='choice']");

                        mcInputs.forEach(input => input.required = false);
                        if (tfSelect) tfSelect.required = false;

                        if (type === "multiple") {
                            mcInputs.forEach(input => input.required = true);
                        } else if (type === "truefalse") {
                            if (tfSelect) tfSelect.required = true;
                        }
                    });
                });
        });
    });

    // Submit for edit form
    const editForm = document.getElementById("editQuizForm");
    if (editForm) {
        editForm.addEventListener("submit", function (e) {
            editForm.querySelectorAll(".edit-question-block").forEach(block => {
                const type = block.querySelector("input[name='questionType[]']").value;
                const tfSelect = block.querySelector("select[name='correctAnswer[]']");
                const mcInputs = block.querySelectorAll("input[name^='choice']");

                mcInputs.forEach(input => input.required = false);
                if (tfSelect) tfSelect.required = false;

                if (type === "multiple") {
                    mcInputs.forEach(input => input.required = true);
                } else if (type === "truefalse") {
                    if (tfSelect) tfSelect.required = true;
                }
            });

            if (!editForm.checkValidity()) {
                e.preventDefault();
                editForm.reportValidity();
            }
        });
    }
}
