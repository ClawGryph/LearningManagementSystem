function initCourseTitle() {
    const urlParams = new URLSearchParams(window.location.search);
    const courseID = urlParams.get('courseID');

    if (courseID) {
        fetch(`../action/getCourseName.php?courseID=${courseID}`)
            .then(response => response.text())
            .then(courseName => {
                const titleElement = document.querySelector('#subject-title');
                if (titleElement) {
                    titleElement.textContent = courseName;
                }
            });
    }
}
