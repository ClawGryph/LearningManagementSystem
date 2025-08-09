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
}