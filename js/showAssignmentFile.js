function initShowAssignmentFile(){
    const fileInput = document.getElementById('assignmentFile');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    fileInput.addEventListener('change', function () {
        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = fileInput.files[0].name;
        } else {
            fileNameDisplay.textContent = 'No file chosen';
        }
    });
}