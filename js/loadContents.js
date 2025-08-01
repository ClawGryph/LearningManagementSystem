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
        'subject-landingpage.php' : 'Subject | Overview',
        'subject-task-progress.php' : 'Subject | Task Progress'
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
            if(normalizedUrl === 'admin-create-class.php' && typeof initClass === 'function'){
                initClass();
                setTimeout(() => {
                    if (typeof initHideSidebarOnClick === 'function') initHideSidebarOnClick();
                }, 0);
            }
            if(normalizedUrl === 'admin-create-courses.php' && typeof initCourse === 'function') {
                initCourse();
                setTimeout(() => {
                    if (typeof initHideSidebarOnClick === 'function') initHideSidebarOnClick();
                }, 0);
            }
            if(normalizedUrl === 'admin-instructor-courses.php' && typeof initHideSidebarOnClick === 'function') {
                setTimeout(() => {
                    initHideSidebarOnClick();
                }, 0);
            }
            if(normalizedUrl === 'admin-lm-lists.php' && typeof initLMLists === 'function') {
                initLMLists();
                setTimeout(() => {
                    if (typeof initHideSidebarOnClick === 'function') initHideSidebarOnClick();
                }, 0);
            }
            if(normalizedUrl === 'admin-instructor-load.php' && typeof initInstructorLoad === 'function') {
                initInstructorLoad();
                setTimeout(() => {
                    if (typeof initHideSidebarOnClick === 'function') initHideSidebarOnClick();
                }, 0);
            }
            if(normalizedUrl === 'admin-notification.php' && typeof initCheckAll === 'function') {
                setTimeout(() => {
                    initCheckAll();
                    if (typeof initHideSidebarOnClick === 'function') initHideSidebarOnClick();
                }, 0);
            }
            if(normalizedUrl === 'instructor-classes.php' && typeof initHideSidebarOnClick === 'function') {
                setTimeout(() => {
                    initHideSidebarOnClick();
                }, 0);
            }
            if(normalizedUrl === 'instructor-create-quiz.php' && typeof initQuiz === 'function'){
                initQuiz();
                setTimeout(() => {
                    if (typeof initQuestionToggle === 'function') initQuestionToggle();
                    if (typeof initHideSidebarOnClick === 'function') initHideSidebarOnClick();
                }, 0);
            }
            if(normalizedUrl === 'instructor-create-assignment.php' && typeof initAssignment === 'function' && typeof initHideSidebarOnClick === 'function'){
                initAssignment();
                setTimeout(() => {
                    initHideSidebarOnClick();
                    if (typeof initShowAssignmentFile === 'function') initShowAssignmentFile();
                }, 0);
            }
            if(normalizedUrl === 'instructor-create-activity.php' && typeof initActivity === 'function') {
                initActivity();
                setTimeout(() => {
                    if (typeof initHideSidebarOnClick === 'function') initHideSidebarOnClick();
                }, 0);
            }
            if(normalizedUrl === 'instructor-upload-lm.php' && typeof initMaterials === 'function'){
                initMaterials();
                setTimeout(() => {
                    if (typeof initHideSidebarOnClick === 'function') initHideSidebarOnClick();
                    if (typeof initShowAssignmentFile === 'function') initShowAssignmentFile();
                }, 0);
            }
            if(normalizedUrl === 'subject-task-progress.php' && typeof initScoreBar === 'function'){
                initScoreBar();
                setTimeout(() => {
                    if (typeof initHideSidebarOnClick === 'function') initHideSidebarOnClick();
                }, 0);
            }
        });
    }


    //Auto-load notification page when successully logged in (Admin, Instructor, Student)
    const isOnSubjectLandingPage = window.location.pathname.includes("subject-landingpage.php");

    if (!isOnSubjectLandingPage) {
        switch(currentUserRole) {
            case 'admin':
                loadPage('admin-notification.php');
                break;
            case 'instructor':
                loadPage('instructor-classes.php');
                break;
            default:
                alert('Role not recognized or not set.');
                break;
        }
    }else{
        loadPage('subject-task-progress.php');
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