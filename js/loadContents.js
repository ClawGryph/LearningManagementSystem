document.addEventListener("DOMContentLoaded", function() {
    // Title for the page
    const titleMap = {
        //ADMIN PAGES
        'admin-notification.php' : 'Admin | Notification',
        'admin-create-courses.php' : 'Admin | Create Course',
        'admin-instructor-courses.php' : 'Admin | Add Instructor to Course',
        'admin-lm-lists.php' : 'Admin | Materials Lists'
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
        });
    }

    //Auto-load notification page when successully logged in (Admin, Instructor, Student)
    const isAdminPage = document.querySelector('a[data-content="admin-notification.php"]');

    if(isAdminPage) {
        loadPage('admin-notification.php');
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