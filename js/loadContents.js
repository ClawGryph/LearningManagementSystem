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
        'instructor-create-assignment.php' : 'Instructor | Assignment'
    };

    const mainContent = document.getElementById('main-content');

    function loadPage(contentUrl){
        fetch(contentUrl)
        .then(response => response.text())
        .then(html => {
            mainContent.innerHTML = html;

            const normalizedUrl = contentUrl.split('/').pop();
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
            if(normalizedUrl === 'admin-instructor-load.php' && typeof initInstructorLoad === 'function') {
                initInstructorLoad();
                setTimeout(() => {
                    if (typeof initHideSidebarOnClick === 'function') initHideSidebarOnClick();
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
        });
    }

    //Auto-load notification page when successully logged in (Admin, Instructor, Student)
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

    document.addEventListener('click', function(e){
        const link = e.target.closest('a[data-content]');
        if(link) {
            e.preventDefault();
            const contentUrl = link.getAttribute('data-content');
            loadPage(contentUrl);
        }
    })
});