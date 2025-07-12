function initHideSidebarOnClick(){
    const sidebar = document.querySelector('.sidebar');
    const sidebarBtn = document.querySelector('.fa-bars');

    sidebarBtn.addEventListener('click', () => {
        sidebar.classList.toggle('close');
    });
}