document.addEventListener('DOMContentLoaded', function() {
    const notifIcon = document.querySelector('.notif-bell');
    const notifDetails = document.querySelector('.navigation .navigation__links .navigation__notif .notif_details');
    const notifLI = document.querySelector('.navigation__notif');
    const navPanel = document.querySelector('.navigation .navigation__links .navbar-panel');
    const sidebar = document.querySelector('.sidebar');
    const sidebarPanel = sidebar.querySelector('.logo-details .sidebar-panel');

     // --- Notification Toggle ---
    notifIcon?.addEventListener('click', function(event) {
        event.preventDefault();
        notifDetails.classList.toggle('hidden');
        notifLI.classList.toggle('hidden');
    });

    // --- Sidebar Toggle ---
    sidebarPanel?.addEventListener('click', function() {
        if (window.innerWidth >= 993) {
            // Desktop behavior
            sidebar.classList.toggle('close');
            sidebar.classList.remove('hidden');
        } else {
            // Mobile behavior
            sidebar.classList.toggle('hidden');
        }
    });

    // --- Open sidebar when mobile nav button is clicked ---
    navPanel?.addEventListener('click', function() {
        sidebar.classList.remove('hidden');
    });

    // --- Close notifications when clicking outside ---
    document.addEventListener('click', function(event) {
        if (!notifLI.contains(event.target) && !notifIcon.contains(event.target)) {
            notifDetails.classList.add('hidden');
            notifLI.classList.add('hidden');
        }
    });

    // --- Resize handler ---
    function handleResize() {
        if (window.innerWidth >= 993) {
            // On desktop → show sidebar but keep it closed
            sidebar.classList.remove('hidden');
            sidebar.classList.add('close');
        } else {
            // On mobile → hide sidebar completely
            sidebar.classList.add('hidden');
            sidebar.classList.remove('close');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize();
});