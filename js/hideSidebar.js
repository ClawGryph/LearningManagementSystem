document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.sidebar');
    const sidebarBtn = document.querySelector('.fa-bars');

    sidebarBtn.addEventListener('click', () => {
        sidebar.classList.toggle('close');
    });

    // Check on load
    if (window.innerWidth >= 993) {
        sidebar.classList.add('close');
    }

    // Optional: also re-check when window is resized
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 993) {
            sidebar.classList.add('close');
        } else {
            sidebar.classList.remove('close');
        }
    });
});