function initCheckAll(){
    document.getElementById("checkAll").addEventListener("change", function () {
        document.querySelectorAll(".notification-checkbox").forEach(cb => cb.checked = this.checked);
    });
}