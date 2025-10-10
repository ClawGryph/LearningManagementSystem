function initEnrolledStudents() {
    $(document).on('click', '.home-contentBtn', function() {
        const courseId = $(this)
            .closest('.course-card-form')
            .find('input[name="instructor_courseID"]')
            .val();

        $.ajax({
            url: '../action/getEnrolledStudents.php',
            type: 'POST',
            data: { instructor_courseID: courseId },
            success: function(response) {
                $('#studentModal').html(response);
                $('#loadingOverlay').addClass('active');
            },
            error: function() {
                $('#studentModal').html('<p>Error loading students.</p>');
            }
        });
    });

    //Recommended: delegated event for close button
    $(document).on('click', '#closeBtn', function() {
            $('#loadingOverlay').removeClass('active');
        });

        // Delegated event for delete buttons
        $(document).on('click', '.deleteBtn', function() {
        const li = $(this).closest('li');
        const studentId = li.data('student-id');
        const courseId = $(this)
            .closest('.popup-box')
            .find('input[name="instructor_courseID"]')
            .val(); // or pass from context if not inside popup

        if (!confirm('Are you sure you want to remove this student from the class?')) return;

        $.ajax({
            url: '../action/removeEnrolledStudents.php',
            type: 'POST',
            data: {
                student_id: studentId,
                instructor_courseID: courseId
            },
            success: function(response) {
                if (response.trim() === "success") {
                    li.fadeOut(300, function() { $(this).remove(); });
                    $('.home-contentBtn').trigger('click');
                } else {
                    alert('Error removing student.');
                }
            },
            error: function() {
                alert('Server error while removing student.');
            }
        });
    });
}