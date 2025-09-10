function initScoreBar() {
    fetch('../action/get-task-statistics.php')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error loading data:', data.error);
                return;
            }

            document.getElementById("courseTitle").innerText = data.courseName;
            document.getElementById("completedCount").innerText = data.taskCounts.completed;
            document.getElementById("incompleteCount").innerText = data.taskCounts.incomplete;
            document.getElementById("overdueCount").innerText = data.taskCounts.overdue;
            document.getElementById("totalCount").innerText = data.taskCounts.total;

            let scoreChart;
            function updateScoreChart(filter = 'all', search = '') {
                const studentScoreData = data.studentScoresByType[filter] || [];
                const filtered = search
                    ? studentScoreData.filter(s => s.name.toLowerCase().includes(search.toLowerCase()))
                    : studentScoreData;

                const labels = filtered.map(s => s.name);
                const scores = filtered.map(s => s.score);

                if (scoreChart) scoreChart.destroy();

                const barCtx = document.getElementById('scoreChart').getContext('2d');
                scoreChart = new Chart(barCtx, {
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
                        },
                        plugins: {
                            legend: { labels: { color: '#018143ff' } }
                        }
                    }
                });
            }

            updateScoreChart('all');

            const donutCtx = document.getElementById('taskTypeDonutChart').getContext('2d');
            let donutChart;

            function updateDonutChart(filter = 'all', search = '') {
                let rawData = [];
                if (filter === 'quiz') rawData = data.taskBreakdown.quiz;
                else if (filter === 'activity') rawData = data.taskBreakdown.activity;
                else if (filter === 'assignment') rawData = data.taskBreakdown.assignment;
                else rawData = [...data.taskBreakdown.quiz, ...data.taskBreakdown.activity, ...data.taskBreakdown.assignment];

                const titles = new Set();
                const counts = {};

                // Only include tasks for the searched student
                const relevantTasks = data.studentTaskStatus.filter(t => 
                    (search === '' || t.studentName.toLowerCase().includes(search.toLowerCase())) &&
                    (filter === 'all' || t.type === filter)
                );

                relevantTasks.forEach(task => {
                    if (!counts[task.title]) counts[task.title] = 0;
                    counts[task.title]++;
                    titles.add(task.title);
                });

                const chartLabels = [...titles];
                const chartData = chartLabels.map(title => counts[title]);

                if (chartData.length === 0) {
                    chartData.push(1);
                    chartLabels.push('No tasks yet');
                }

                if (donutChart) donutChart.destroy();

                donutChart = new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Tasks',
                            data: chartData,
                            backgroundColor: ['#4CAF50', '#FFC107', '#FF5722', '#3F51B5', '#9C27B0', '#00BCD4'],
                            borderColor: '#011627d3',
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


            const barCtx2 = document.getElementById('tabsOpenBarChart').getContext('2d');
            let barChart;

            function updateTabsOpenBarChart(filter = 'all', search = '') {
                let rawData = [];
                if (filter === 'quiz') rawData = data.taskBreakdown.quiz;
                else if (filter === 'activity') rawData = data.taskBreakdown.activity;
                else if (filter === 'assignment') rawData = data.taskBreakdown.assignment;
                else rawData = [...data.taskBreakdown.quiz, ...data.taskBreakdown.activity, ...data.taskBreakdown.assignment];

                const tabsCount = {};
                const titles = new Set();

                const relevantTasks = data.studentTaskStatus.filter(t => 
                    (search === '' || t.studentName.toLowerCase().includes(search.toLowerCase())) &&
                    (filter === 'all' || t.type === filter)
                );

                relevantTasks.forEach(task => {
                    const found = rawData.find(r => r.title === task.title);
                    if (found) {
                        if (!tabsCount[task.title]) tabsCount[task.title] = 0;
                        tabsCount[task.title] = found.tabs_open;  // Since it's grouped
                        titles.add(task.title);
                    }
                });

                const chartLabels = [...titles];
                const chartData = chartLabels.map(title => tabsCount[title]);

                if (chartData.length === 0) {
                    chartData.push(0);
                    chartLabels.push('No data');
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
                            legend: { display: false },
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
                                title: { display: true, text: 'Number of Tabs Opened'}
                            },
                            x: {
                                title: { display: true, text: 'Assessment Title'}
                            }
                        }
                    }
                });
            }


            const studentTasks = data.studentTaskStatus || [];
            const statusMap = {
                submitted: 'completed',
                graded: 'completed',
                assigned: 'incomplete',
                in_progress: 'incomplete',
                late: 'overdue'
            };

            const filterSelect = document.getElementById('taskTypeFilter');
            const statusFilter = document.getElementById('statusFilter');
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const suggestionsBox = document.getElementById('suggestions');
            const searchMessage = document.getElementById('searchMessage');

            let selectedType = 'all';
            let selectedTitle = 'all';
            let selectedStatus = 'all';
            let searchQuery = '';

            function renderStudentTaskTable(tasks) {
                const tbody = document.querySelector('#studentTaskTable tbody');
                tbody.innerHTML = '';

                const filtered = tasks.filter(task => {
                    const taskStatus = statusMap[task.status] || 'incomplete';
                    const matchesStatus = selectedStatus === 'all' || taskStatus === selectedStatus;
                    const matchesType = selectedType === 'all' || task.type === selectedType;
                    const matchesTitle = selectedTitle === 'all' || task.title === selectedTitle;
                    const matchesName = searchQuery === '' || task.studentName.toLowerCase().includes(searchQuery.toLowerCase());
                    return matchesStatus && matchesType && matchesTitle && matchesName;
                });

                if (filtered.length === 0) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = '<td colspan="6">No matching records</td>';
                    tbody.appendChild(tr);
                    return;
                }

                filtered.forEach(task => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><img src="../uploads/${task.profileImage || 'default.png'}" 
                            alt="Profile" 
                            class="profile-img">
                        </td>
                        <td>${task.studentName}</td>
                        <td>${task.title}</td>
                        <td>${task.type}</td>
                        <td>${statusMap[task.status]}</td>
                        <td>${task.score} / ${task.max_score ?? '-'}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            function updateStudentTable() {
                renderStudentTaskTable(studentTasks);
            }

            function initFilters() {
                statusFilter.addEventListener('change', () => {
                    selectedStatus = statusFilter.value;
                    updateStudentTable();
                });

                filterSelect.addEventListener('change', () => {
                    const groupLabel = filterSelect.selectedOptions[0]?.parentNode?.label?.toLowerCase() || 'all';
                    const labelMap = {
                        quizzes: 'quiz',
                        activities: 'activity',
                        assignments: 'assignment'
                    };

                    if (filterSelect.value === 'all') {
                        selectedType = 'all';
                        selectedTitle = 'all';
                    } else {
                        const parsed = JSON.parse(filterSelect.value);
                        selectedType = parsed.type;
                        selectedTitle = parsed.title;
                    }

                    updateDonutChart(filterSelect.value);
                    updateTabsOpenBarChart(filterSelect.value);
                    updateScoreChart(filterSelect.value);
                    updateStudentTable();
                });

                searchButton.addEventListener('click', () => {
                    const name = searchInput.value.trim();
                    searchQuery = name;
                    if (name === '') {
                        searchMessage.innerText = 'Please enter a student name.';
                    } else {
                        const hasResults = studentTasks.some(task => 
                            task.studentName.toLowerCase().includes(name.toLowerCase())
                        );

                        if (!hasResults) {
                            searchMessage.innerText = `No student found with name "${name}".`;
                        } else {
                            searchMessage.innerText = '';
                        }
                    }
                    updateStudentTable();
                    updateDonutChart(selectedType, searchQuery);
                    updateTabsOpenBarChart(selectedType, searchQuery);
                    updateScoreChart(selectedType, searchQuery);
                });

                searchInput.addEventListener('input', () => {
                    const query = searchInput.value.toLowerCase();
                    suggestionsBox.innerHTML = '';
                    if (query.length < 1) return;

                    const uniqueNames = [...new Set(studentTasks.map(t => t.studentName))];
                    const filteredNames = uniqueNames.filter(name => name.toLowerCase().includes(query));

                    filteredNames.forEach(name => {
                        const div = document.createElement('div');
                        div.textContent = name;
                        div.addEventListener('click', () => {
                        searchInput.value = name;
                        suggestionsBox.innerHTML = '';
                        searchQuery = name;
                        updateStudentTable();
                        updateDonutChart(selectedType, searchQuery);
                        updateTabsOpenBarChart(selectedType, searchQuery);
                        updateScoreChart(selectedType, searchQuery);
                    });
                        suggestionsBox.appendChild(div);
                    });
                });

                document.addEventListener('click', e => {
                    if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                        suggestionsBox.innerHTML = '';
                    }
                });

                searchInput.addEventListener('input', () => {
                    if (searchInput.value.trim() === '') {
                        searchQuery = '';
                        searchMessage.innerText = '';
                        suggestionsBox.innerHTML = '';
                        updateStudentTable();
                        updateDonutChart(selectedType, searchQuery);
                        updateTabsOpenBarChart(selectedType, searchQuery);
                        updateScoreChart(selectedType, searchQuery);
                    }
                });
            }

            // Populate filter options and initialize charts
            const defaultOption = document.createElement('option');
            defaultOption.value = 'all';
            defaultOption.textContent = 'All assessments';
            filterSelect.appendChild(defaultOption);

            function addGroup(label, tasks, type) {
                if (tasks.length === 0) return;
                const group = document.createElement('optgroup');
                group.label = label;
                tasks.forEach(task => {
                    const opt = document.createElement('option');
                    opt.value = JSON.stringify({ type, title: task.title });
                    opt.textContent = task.title;
                    group.appendChild(opt);
                });
                filterSelect.appendChild(group);
            }

            addGroup('Quizzes', data.taskBreakdown.quiz, 'quiz');
            addGroup('Activities', data.taskBreakdown.activity, 'activity');
            addGroup('Assignments', data.taskBreakdown.assignment, 'assignment');

            updateDonutChart('all');
            updateTabsOpenBarChart('all');
            updateStudentTable();
            initFilters();
        })
        .catch(err => {
            console.error('Fetch error:', err);
        });
}