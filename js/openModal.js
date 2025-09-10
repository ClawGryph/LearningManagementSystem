function initOpenModal(){
    const overlay = document.getElementById("loadingOverlay");
    const enroleeModal = document.getElementById("enrolee");
    const showBtn = document.getElementById("showEnrolees");
    const closeBtn = document.getElementById("closeBtn");

    showBtn.addEventListener("click", () => {
        overlay.classList.add("active");
    });

    closeBtn.addEventListener("click", () => {
        overlay.classList.remove("active");
    });
}