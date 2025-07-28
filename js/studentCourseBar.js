function initScoreBar() {
    fetch('../action/get-task-statistics.php')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error loading data:', data.error);
                return;
            }

            // Render course name
            document.getElementById("courseTitle").innerText = data.courseName;

            // Render task counts
            document.getElementById("completedCount").innerText = data.taskCounts.completed;
            document.getElementById("incompleteCount").innerText = data.taskCounts.incomplete;
            document.getElementById("overdueCount").innerText = data.taskCounts.overdue;
            document.getElementById("totalCount").innerText = data.taskCounts.total;

            // Bar Chart for Student Scores (unchanged)
            const studentScoreData = data.studentScores;
            const labels = studentScoreData.map(s => s.name);
            const scores = studentScoreData.map(s => s.score);

            const barCtx = document.getElementById('scoreChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Average Score',
                        data: scores,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });

            // Donut Chart setup
            const donutCtx = document.getElementById('taskTypeDonutChart').getContext('2d');
            let donutChart;

            function updateDonutChart(filter = 'all') {
                let chartData = [];
                let chartLabels = [];

                if (filter === 'quiz') {
                    chartData = data.taskBreakdown.quiz.map(q => q.count);
                    chartLabels = data.taskBreakdown.quiz.map(q => q.title);
                } else if (filter === 'activity') {
                    chartData = data.taskBreakdown.activity.map(a => a.count);
                    chartLabels = data.taskBreakdown.activity.map(a => a.title);
                } else if (filter === 'assignment') {
                    chartData = data.taskBreakdown.assignment.map(a => a.count);
                    chartLabels = data.taskBreakdown.assignment.map(a => a.title);
                }
                else {
                    const all = [...data.taskBreakdown.quiz, ...data.taskBreakdown.activity, ...data.taskBreakdown.assignment];
                    chartData = all.map(item => item.count);
                    chartLabels = all.map(item => item.title);
                }

                if (chartData.length === 0) {
                    chartData = [1];
                    chartLabels = ['No tasks yet'];
                }

                if (donutChart) donutChart.destroy();

                donutChart = new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Tasks',
                            data: chartData,
                            backgroundColor: [
                                '#4CAF50', '#FFC107', '#FF5722', '#3F51B5', '#9C27B0', '#00BCD4'
                            ],
                            borderColor: '#fff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Populate <select> with titles
            const filterSelect = document.getElementById('taskTypeFilter');
            if (filterSelect) {
                filterSelect.innerHTML = ''; // Clear existing

                const defaultOption = document.createElement('option');
                defaultOption.value = 'all';
                defaultOption.textContent = 'All (Quiz + Activity + Assignment)';
                filterSelect.appendChild(defaultOption);

                function addGroup(label, tasks, type) {
                    if (tasks.length === 0) return;
                    const group = document.createElement('optgroup');
                    group.label = label;
                    tasks.forEach(task => {
                        const opt = document.createElement('option');
                        opt.value = type;
                        opt.textContent = task.title;
                        group.appendChild(opt);
                    });
                    filterSelect.appendChild(group);
                }

                addGroup('Quizzes', data.taskBreakdown.quiz, 'quiz');
                addGroup('Activities', data.taskBreakdown.activity, 'activity');
                addGroup('Assignments', data.taskBreakdown.assignment, 'assignment');

                // Add change listener after populating
                filterSelect.addEventListener('change', () => {
                    const selected = filterSelect.value;
                    updateDonutChart(selected);
                });
            }

            // Initial donut chart render
            updateDonutChart('all');

            //TABS OPEN BAR CHART
            const barCtx2 = document.getElementById('tabsOpenBarChart').getContext('2d');
            let barChart;

            function updateTabsOpenBarChart(filter = 'all') {
                let chartData = [];
                let chartLabels = [];

                const getFilteredData = () => {
                    if (filter === 'quiz') return data.taskBreakdown.quiz;
                    if (filter === 'activity') return data.taskBreakdown.activity;
                    if (filter === 'assignment') return data.taskBreakdown.assignment;
                    return [...data.taskBreakdown.quiz, ...data.taskBreakdown.activity, ...data.taskBreakdown.assignment];
                };

                const filtered = getFilteredData();
                chartData = filtered.map(item => item.tabs_open);
                chartLabels = filtered.map(item => item.title);

                if (chartData.length === 0) {
                    chartData = [0];
                    chartLabels = ['No data'];
                }

                if (barChart) barChart.destroy();

                barChart = new Chart(barCtx2, {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Tabs Opened',
                            data: chartData,
                            backgroundColor: '#42A5F5',
                            borderColor: '#1E88E5',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Tabs Opened: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Tabs Opened'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Assessment Title'
                                }
                            }
                        }
                    }
                });
            }

            updateTabsOpenBarChart('all');

            filterSelect.addEventListener('change', () => {
                const selected = filterSelect.value;
                updateDonutChart(selected);
                updateTabsOpenBarChart(selected);
            });


        })
        .catch(err => {
            console.error('Fetch error:', err);
        });
}
