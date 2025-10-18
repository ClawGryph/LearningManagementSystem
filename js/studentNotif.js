$(document).ready(function() {
    $('#markReadForm').on('submit', function(e) {
        e.preventDefault(); // stop normal form submission

        // Collect all notification IDs
        const notifications = [];
        $('.notif-item').each(function() {
            const id = $(this).data('notif-id');
            if (id) notifications.push(id);
        });

        $.ajax({
            url: '../action/markStudentNotificationsRead.php',
            type: 'POST',
            data: { 'notifications[]': notifications },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update badge
                    $('.notif-badge').text('0');

                    // Clear notification list
                    $('.notif-list').html('<p class="no-notif">No new notifications.</p>');
                } else {
                    console.warn('No notifications were updated.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    });
});