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
            url: '../action/markRead.php',
            type: 'POST',
            data: { notifications: notifications },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update UI: set badge to 0, clear notifications, and show message
                    $('.notif-badge').text('0');
                    $('.notif-list').html('<p class="no-notif">No new notifications.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    });
});
