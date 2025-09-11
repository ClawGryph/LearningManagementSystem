function initEnroleesQueue(){
    document.querySelectorAll('.approve-btn, .reject-btn').forEach(button => {
        button.addEventListener('click', function () {
            const requestID = this.getAttribute('data-id');
            const action = this.classList.contains('approve-btn') ? 'approved' : 'rejected';

            fetch('../action/decision.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `requestID=${requestID}&action=${action}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.toLowerCase().includes('success')) {
                    this.closest('tr').remove();
                    alert(`Enrolee ${action}!`);
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