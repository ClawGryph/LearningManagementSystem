document.addEventListener("DOMContentLoaded", function () {
    let arrow = document.querySelectorAll(".arrow");
    let sidebar = document.querySelector(".sidebar");
    let sidebarBtn = document.querySelector(".fa-bars");

    for(var i = 0; i < arrow.length; i++) {
        arrow[i].addEventListener("click", (e)=>{
            let arrowParent = e.target.parentElement.parentElement; // li > ul > li
            arrowParent.classList.toggle("showMenu");
        });
    }

    sidebarBtn.addEventListener("click", ()=>{
        sidebar.classList.toggle("close");
    });
});