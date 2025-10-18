$(document).ready(function() {
    $('#markReadForm').on('submit', function(e) {
        e.preventDefault(); // prevent normal form submission

        $.ajax({
            url: '../action/markNotificationsRead.php',
            type: 'POST',
            success: function(response) {
                if (response.trim() === 'success') {
                    // Remove notification badge or set it to 0
                    $('.notif-badge').text('0');

                    // Clear notification list text
                    $('.notif-list').html('<p class="no-notif">No new notifications.</p>');
                }
            },
            error: function() {
                alert('Failed to mark notifications as read.');
            }
        });
    });
});
