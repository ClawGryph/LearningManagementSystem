function initLMLists(){
    document.querySelectorAll('.approve-btn, .reject-btn').forEach(button => {
    button.addEventListener('click', function () {
        const lmID = this.getAttribute('data-id');
        const action = this.classList.contains('approve-btn') ? 'approved' : 'rejected';

        fetch('../action/updateMaterialStatus.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `lmID=${lmID}&action=${action}`
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === 'success') {
                // Optionally remove the row or refresh the page
                this.closest('tr').remove();
                alert(`Learning material ${action}!`);
                location.reload();
            } else {
                alert('Error: ' + data);
            }
        })
        .catch(err => {
            alert('Request failed');
            console.error(err);
        });
    });
});
}