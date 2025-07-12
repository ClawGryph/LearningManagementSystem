document.addEventListener("DOMContentLoaded", function () {
    const profileImage = document.getElementById("profileImagePreview");
    const profileInput = document.getElementById("profileImageRealInput");

    profileImage.addEventListener("mouseenter", () => {
        profileImage.style.cursor = "pointer";
    });

    profileImage.addEventListener("click", () => {
        profileInput.click();
    });

    profileInput.addEventListener("change", function () {
        const file = this.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append("profileImage", file);

        fetch("../action/uploadProfileImage.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(response => {
            if (response.startsWith("success:")) {
                const filename = response.split(":")[1];
                profileImage.src = "../uploads/" + filename;
            } else {
                alert("Upload failed: " + response);
            }
        });
    });
});
