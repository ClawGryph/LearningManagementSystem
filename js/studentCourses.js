function initStudentCourses(){
    // OPEN MODAL
    const overlay = document.getElementById("loadingOverlay");
    const closeBtn = document.getElementById("closeBtn");
    const joinBtn = document.getElementById("joinButton");

    joinBtn.addEventListener("click", () => {
        overlay.classList.add("active");
    })

    closeBtn.addEventListener("click", () => {
        overlay.classList.remove("active");
    });

    // SUBMITTING CODE TO PHP
    document.getElementById('submitCode').addEventListener('click', function() {
    const classCode = document.getElementById('inputCode').value.trim();

    if (classCode === '') {
        document.getElementById('searchMessage').textContent = "Please enter a class code.";
        return;
    }

    fetch('../action/joinClass.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'classCode=' + encodeURIComponent(classCode)
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('searchMessage').textContent = data.message;
            if (data.success) {
                document.getElementById('searchMessage').style.color = 'green';
                // Optionally reload page to show new class
                setTimeout(() => location.reload(), 1500);
            } else {
                document.getElementById('searchMessage').style.color = 'red';
            }
        })
        .catch(err => {
            document.getElementById('searchMessage').textContent = "An error occurred. Please try again.";
            document.getElementById('searchMessage').style.color = 'red';
        });
    });
}