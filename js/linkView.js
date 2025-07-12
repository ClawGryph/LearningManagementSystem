document.addEventListener("DOMContentLoaded", function () {
    let arrow = document.querySelectorAll(".arrow");

    for(var i = 0; i < arrow.length; i++) {
        arrow[i].addEventListener("click", (e)=>{
            let arrowParent = e.target.parentElement.parentElement; // li > ul > li
            arrowParent.classList.toggle("showMenu");
        });
    }
});