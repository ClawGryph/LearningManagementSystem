function initNewCourse(){

    hideSidebarOnClick();
    toggleUserManagementView();

    function toggleUserManagementView() {
        const firstPage = document.querySelector('.first-page');
        const courseModal = document.getElementById('courseModal');

        //Show modal, hide first page
        document.getElementById('coursePage').addEventListener('click', function() {
            firstPage.classList.add('hidden');
            courseModal.classList.add('active');
        });

        //Hide modal, show first page
        document.getElementById('addNewCourse').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent form submission
            courseModal.classList.remove('active');
            firstPage.classList.remove('hidden');
        });
    }

    // Hide Sidebar on click
    function hideSidebarOnClick() {
        const sidebar = document.querySelector('.sidebar');
        const sidebarBtn = document.querySelector('.fa-bars');

        sidebarBtn.addEventListener('click', () => {
            sidebar.classList.toggle('close');
        });
    }
}