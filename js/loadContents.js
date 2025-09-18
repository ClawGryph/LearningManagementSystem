document.addEventListener("DOMContentLoaded", function() {
    // Title for the page
    const titleMap = {
        //ADMIN PAGES
        'admin-notification.php' : 'Admin | Notification',
        'admin-create-class.php' : 'Admin | Class',
        'admin-create-courses.php' : 'Admin | Create Course',
        'admin-instructor-courses.php' : 'Admin | Add Instructor to Course',
        'admin-instructor-load.php' : "Admin | Instructor Load",
        'admin-lm-lists.php' : 'Admin | Materials Lists',
        'instructor-classes.php' : 'Instructor | Classes',
        'instructor-create-quiz.php' : 'Instructor | Quiz',
        'instructor-create-assignment.php' : 'Instructor | Assignment',
        'instructor-create-activity.php' : 'Instructor | Activity',
        'instructor-upload-lm.php' : 'Instructor | Upload',
        'subject-approval.php' : 'Subject | Enrolee',
        'subject-landingpage.php' : 'Subject | Overview',
        'subject-task-progress.php' : 'Subject | Task Progress',
        'subject-submitted-assignment.php' : 'Subject | Assignment',
        'subject-submitted-activity.php' : 'Subject | Activity',
        'subject-submitted-quiz.php' : 'Subject | Quiz',
        'student-notification.php' : 'Student | Notification',
        'student-courses.php' : 'Student | Courses',
        'student-subject-myProgess.php' : 'Student | My Progress',
        'student-subject-assignment.php' : 'Student | Assignment',
        'student-subject-quiz.php' : 'Student | Quiz',
    };

    const mainContent = document.getElementById('main-content');

    function loadPage(contentUrl){
        fetch(contentUrl)
        .then(response => response.text())
        .then(html => {
            mainContent.innerHTML = html;

            const normalizedUrl = contentUrl.split('/').pop().split('?')[0];

            if(titleMap[normalizedUrl]) {
                document.title = titleMap[normalizedUrl];
            }

            mainContent.querySelectorAll('script').forEach(script => {
                if(script.textContent) eval(script.textContent);
            });

            // Initialize specific functions after loading the page
            // ADMIN PAGES
            if(normalizedUrl === 'admin-notification.php' && typeof initClock === 'function'){
                initClock();
            }
            if(normalizedUrl === 'admin-create-class.php' && typeof initClass === 'function'){
                initClass();
                setTimeout(() => {
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'admin-create-courses.php' && typeof initCourse === 'function') {
                initCourse();
                setTimeout(() => {
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'admin-instructor-courses.php' && typeof initClock === 'function'){
                initClock();
            }
            if(normalizedUrl === 'admin-lm-lists.php' && typeof initLMLists === 'function') {
                initLMLists();
                setTimeout(() => {
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'admin-instructor-load.php' && typeof initInstructorLoad === 'function') {
                initInstructorLoad();
                setTimeout(() => {
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }

            // INSTRUCTOR PAGES
            if(normalizedUrl === 'instructor-notification.php' && typeof initClock === 'function'){
                initClock();
            }
            if(normalizedUrl === 'instructor-classes.php' && typeof initClock === 'function'){
                initClock();
            }
            if(normalizedUrl === 'instructor-create-quiz.php' && typeof initQuiz === 'function'){
                initQuiz();
                setTimeout(() => {
                    if (typeof initQuestionToggle === 'function') initQuestionToggle();
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'instructor-create-assignment.php' && typeof initAssignment === 'function'){
                initAssignment();
                setTimeout(() => {
                    if (typeof initShowAssignmentFile === 'function') initShowAssignmentFile();
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'instructor-create-activity.php' && typeof initActivity === 'function') {
                initActivity();
                setTimeout(() => {
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'instructor-upload-lm.php' && typeof initMaterials === 'function'){
                initMaterials();
                setTimeout(() => {
                    if (typeof initShowAssignmentFile === 'function') initShowAssignmentFile();
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'subject-approval.php' && typeof initEnroleesQueue === 'function'){
                initEnroleesQueue();
                setTimeout(() => {
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'subject-task-progress.php' && typeof initScoreBar === 'function'){
                initScoreBar();
                setTimeout(() => {
                    if(typeof initOpenModal === 'function') initOpenModal();
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'subject-submitted-activity.php' && typeof initClock === 'function'){
                initClock();
            }
            if(normalizedUrl === 'subject-submitted-assignment.php' && typeof initClock === 'function'){
                initClock();
            }
            if(normalizedUrl === 'subject-submitted-quiz.php' && typeof initClock === 'function'){
                initClock();
            }

            // STUDENT PAGES
            if(normalizedUrl === 'student-notification.php' && typeof initCheckAll === 'function') {
                setTimeout(() => {
                    initCheckAll();
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'student-courses.php' && typeof initStudentCourses === 'function') {
                setTimeout(() => {
                    initStudentCourses();
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'student-subject-myProgess.php' && typeof initProgressBars === 'function') {
                initProgressBars();
                setTimeout(() => {
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'student-subject-activity.php' && typeof initClock === 'function'){
                initClock();
            }
            if(normalizedUrl === 'student-subject-assignment.php' && typeof initShowAssignmentFile === 'function') {
                initShowAssignmentFile();
                setTimeout(() => {
                    if (typeof initClock === 'function') initClock();
                }, 0);
            }
            if(normalizedUrl === 'student-subject-quiz.php' && typeof initClock === 'function'){
                initClock();
            }
            if(normalizedUrl === 'student-subject-materials.php' && typeof initClock === 'function'){
                initClock();
            }
        });
    }


    //Auto-load notification page when successully logged in (Admin, Instructor, Student)
    const isOnSubjectLandingPage = window.location.pathname.includes("subject-landingpage.php");
    const isOnStudentSubjectLandingPage = window.location.pathname.includes("student-subject-landingpage.php");

    if (!isOnSubjectLandingPage && !isOnStudentSubjectLandingPage) {
        switch(currentUserRole) {
            case 'admin':
                loadPage('admin-notification.php');
                break;
            case 'instructor':
                loadPage('instructor-classes.php');
                break;
            case 'student':
                loadPage('student-notification.php');
                break;
            default:
                alert('Role not recognized or not set.');
                break;
        }
    }else if (isOnSubjectLandingPage && currentUserRole === 'instructor') {
        loadPage('subject-task-progress.php');
    }else if (isOnStudentSubjectLandingPage && currentUserRole === 'student') {
        loadPage('student-subject-myProgess.php');
    }

    document.addEventListener('click', function(e){
        const link = e.target.closest('a[data-content]');
        if(link) {
            e.preventDefault();
            const contentUrl = link.getAttribute('data-content');
            loadPage(contentUrl);
        }
    })
});