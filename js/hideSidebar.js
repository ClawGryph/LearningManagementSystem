function initHideSidebarOnClick(){
    const sidebar = document.querySelector('.sidebar');
    const sidebarBtn = document.querySelector('.fa-bars');
    const logoContainer = document.querySelector('.logo-details');
    const logoImg = document.querySelector('.logo-details .logo_img');

    sidebarBtn.addEventListener('click', () => {
        sidebar.classList.toggle('close');
    });

    // Check on load
    if (window.innerWidth <= 1080) {
        sidebar.classList.add('close');
    }

    // Optional: also re-check when window is resized
    window.addEventListener('resize', () => {
        if (window.innerWidth <= 1080) {
            sidebar.classList.add('close');
        } else {
            sidebar.classList.remove('close');
        }
    });
}