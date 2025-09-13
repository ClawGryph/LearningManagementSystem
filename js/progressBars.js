function initProgressBars(){
    let quizChart, assignmentChart, activityChart;

    function createDonutChart(ctx, value, color) {
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [value, 100 - value],
                    backgroundColor: [color, '#e9ecef'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                    title: {
                        display: true,
                        text: value + '%',
                        color: '#333',
                        font: { size: 16, weight: 'bold' }
                    }
                }
            }
        });
    }

    fetch('../action/get-student-progress.php')
        .then(response => response.json())
        .then(data => {
            if (quizChart) quizChart.destroy();
            if (assignmentChart) assignmentChart.destroy();
            if (activityChart) activityChart.destroy();

            quizChart = createDonutChart(document.getElementById('quizChart'), data.quiz, '#ff7f50');
            assignmentChart = createDonutChart(document.getElementById('assignmentChart'), data.assignment, '#28a745');
            activityChart = createDonutChart(document.getElementById('activityChart'), data.activity, '#17a2b8');
        })
        .catch(err => console.error(err));

        $('#studentTaskTable').DataTable({
        paging: false,
        info: false,
        searching: false,
        autoWidth: false,
        columnDefs: [
            { targets: '_all', defaultContent: '-' }
        ],
        initComplete: function () {
            this.api().columns(0).every(function () {
                var column = this;
                var header = $(column.header());

                // Save existing header text
                var title = header.text();

                // Clear and rebuild with title + filter
                header.empty().append(title + '<br/>');

                var select = $('<select><option value="">All</option></select>')
                    .appendTo(header)
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });

                column.data().unique().sort().each(function (d) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            });
        }
    });
}