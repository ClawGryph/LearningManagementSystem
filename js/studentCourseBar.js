function initScoreBar() {
    fetch('../action/get-task-statistics.php')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error loading data:', data.error);
                return;
            }

            // --- safe course title update (only if element exists)
            const courseTitleElem = document.getElementById("courseTitle");
            if (courseTitleElem) courseTitleElem.innerText = data.courseName || '';

            const pb = data.progressBreakdown || { quiz: {}, assignment: {}, activity: {} };

            // ================== SCORE CHART ==================
            let scoreChart;
            function updateScoreChart(filter = 'all', search = '') {
                const studentScoreData = (data.studentScoresByType && data.studentScoresByType[filter]) || [];
                const filtered = search
                    ? studentScoreData.filter(s => s.name && s.name.toLowerCase().includes(search.toLowerCase()))
                    : studentScoreData;

                const labels = filtered.map(s => s.name);
                const scores = filtered.map(s => s.score);

                const scoreCanvas = document.getElementById('scoreChart');
                if (!scoreCanvas) return;
                const barCtx = scoreCanvas.getContext('2d');

                if (scoreChart && typeof scoreChart.destroy === 'function') scoreChart.destroy();

                scoreChart = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels,
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
                            y: { beginAtZero: true, max: 100 }
                        },
                        plugins: {
                            legend: { labels: { color: '#018143ff' } }
                        }
                    }
                });
            }
            console.log("PB data:", pb);

            // ================== PROGRESS DONUTS (with % + completed/total inside, colored) ==================
            const progressDonutCharts = {};

            function createDonutChart(canvasId, completed = 0, total = 0) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return;
                const ctx = canvas.getContext('2d');

                // destroy previous chart
                if (progressDonutCharts[canvasId] && typeof progressDonutCharts[canvasId].destroy === 'function') {
                    progressDonutCharts[canvasId].destroy();
                }

                // compute percentage & arc color
                const percent = total > 0 ? Math.round((completed / total) * 100) : 0;
                let arcColor = '#e53935'; // red <30%
                if (percent > 50) {
                    arcColor = '#4caf50'; // green
                } else if (percent >= 30) {
                    arcColor = '#ff9800'; // orange
                }

                // Custom plugin for center text (uses same arcColor & percent)
                const centerTextPlugin = {
                    id: 'centerText',
                    beforeDraw(chart) {
                        const { width, height, ctx } = chart;
                        ctx.save();

                        // Main percentage text
                        ctx.font = 'bold 18px Arial';
                        ctx.fillStyle = arcColor; // ✅ use arcColor from above
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(percent + '%', width / 2, height / 2 - 8);

                        // Completed / Total text (below %)
                        ctx.font = '14px Arial';
                        ctx.fillStyle = '#666';
                        ctx.fillText(`${completed}/${total}`, width / 2, height / 2 + 12);

                        ctx.restore();
                    }
                };

                progressDonutCharts[canvasId] = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completed', 'Remaining'],
                        datasets: [{
                            data: [completed, Math.max(0, total - completed)],
                            backgroundColor: [arcColor, '#e0e0e0'], // ✅ same arcColor here
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        }
                    },
                    plugins: [centerTextPlugin] // 👈 plugin gets same arcColor
                });
            }

            function updateProgressCharts() {
                // QUIZ
                const quizCompleted = (pb.quiz?.completed || 0);
                const quizTotal = (pb.quiz?.completed || 0) + (pb.quiz?.incomplete || 0) + (pb.quiz?.overdue || 0);

                // ASSIGNMENT
                const assignmentCompleted = (pb.assignment?.completed || 0);
                const assignmentTotal = (pb.assignment?.completed || 0) + (pb.assignment?.incomplete || 0) + (pb.assignment?.overdue || 0);

                // ACTIVITY
                const activityCompleted = (pb.activity?.completed || 0);
                const activityTotal = (pb.activity?.completed || 0) + (pb.activity?.incomplete || 0) + (pb.activity?.overdue || 0);

                // ✅ Create charts
                createDonutChart("quizChart", quizCompleted, quizTotal);
                createDonutChart("assignmentChart", assignmentCompleted, assignmentTotal);
                createDonutChart("activityChart", activityCompleted, activityTotal);
            }

            // ================== TASK TYPE DONUT ==================
            const taskTypeCanvas = document.getElementById('taskTypeDonutChart');
            const donutCtx = taskTypeCanvas ? taskTypeCanvas.getContext('2d') : null;
            let donutChart;
            function updateDonutChart(filter = 'all', search = '') {
                if (!donutCtx) return;

                let rawData = [];
                if (filter === 'quiz') rawData = data.taskBreakdown.quiz || [];
                else if (filter === 'activity') rawData = data.taskBreakdown.activity || [];
                else if (filter === 'assignment') rawData = data.taskBreakdown.assignment || [];
                else rawData = [...(data.taskBreakdown.quiz || []), ...(data.taskBreakdown.activity || []), ...(data.taskBreakdown.assignment || [])];

                const counts = {};
                const titles = new Set();

                const relevantTasks = (data.studentTaskStatus || []).filter(t =>
                    (search === '' || (t.studentName && t.studentName.toLowerCase().includes(search.toLowerCase()))) &&
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
                    chartLabels.push('No tasks yet');
                    chartData.push(1);
                }

                if (donutChart && typeof donutChart.destroy === 'function') donutChart.destroy();
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
                    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                });
            }

            // ================== TABS OPEN BAR ==================
            const tabsCanvas = document.getElementById('tabsOpenBarChart');
            const barCtx2 = tabsCanvas ? tabsCanvas.getContext('2d') : null;
            let barChart;
            function updateTabsOpenBarChart(filter = 'all', search = '') {
                if (!barCtx2) return;

                let rawData = [];
                if (filter === 'quiz') rawData = data.taskBreakdown.quiz || [];
                else if (filter === 'activity') rawData = data.taskBreakdown.activity || [];
                else if (filter === 'assignment') rawData = data.taskBreakdown.assignment || [];
                else rawData = [...(data.taskBreakdown.quiz || []), ...(data.taskBreakdown.activity || []), ...(data.taskBreakdown.assignment || [])];

                const tabsCount = {};
                const titles = new Set();

                const relevantTasks = (data.studentTaskStatus || []).filter(t =>
                    (search === '' || (t.studentName && t.studentName.toLowerCase().includes(search.toLowerCase()))) &&
                    (filter === 'all' || t.type === filter)
                );

                relevantTasks.forEach(task => {
                    const found = rawData.find(r => r.title === task.title);
                    if (found) {
                        tabsCount[task.title] = found.tabs_open;
                        titles.add(task.title);
                    }
                });

                const chartLabels = [...titles];
                const chartData = chartLabels.map(title => tabsCount[title] || 0);

                if (chartData.length === 0) {
                    chartLabels.push('No data');
                    chartData.push(0);
                }

                if (barChart && typeof barChart.destroy === 'function') barChart.destroy();
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
                            tooltip: { callbacks: { label: ctx => `Tabs Opened: ${ctx.raw}` } }
                        },
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Number of Tabs Opened'} },
                            x: { title: { display: true, text: 'Assessment Title'} }
                        }
                    }
                });
            }

            // ================== TASK TABLE ==================
            const studentTasks = data.studentTaskStatus || [];

            const statusMap = {
                submitted: 'completed',
                graded: 'completed',
                assigned: 'incomplete',
                in_progress: 'incomplete',
                late: 'late'
            };

            let selectedType = 'all';
            let selectedTitle = 'all';
            let selectedStatus = 'all';
            let searchQuery = '';

            function renderStudentTaskTable(tasks) {
                const tbody = document.querySelector('#studentTaskTable tbody');
                if (!tbody) return;
                tbody.innerHTML = '';

                const filtered = tasks.filter(task => {
                    let taskStatus = statusMap[task.status] || 'incomplete';
                    
                    if (selectedStatus === 'completed' && taskStatus === 'late') {
                        taskStatus = 'completed';
                    }

                    const matchesStatus = selectedStatus === 'all' || taskStatus === selectedStatus;
                    const matchesType = selectedType === 'all' || task.type === selectedType;
                    const matchesTitle = selectedTitle === 'all' || task.title === selectedTitle;
                    const matchesName = searchQuery === '' || (task.studentName && task.studentName.toLowerCase().includes(searchQuery.toLowerCase()));
                    return matchesStatus && matchesType && matchesTitle && matchesName;
                });

                if (filtered.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6">No matching records</td></tr>';
                    return;
                }

                filtered.forEach(task => {
                    const tr = document.createElement('tr');

                    // special handling for late
                    let displayStatus;
                    if (task.status === 'late') {
                        displayStatus = 'Late';
                    } else if (task.status === 'submitted' || task.status === 'graded') {
                        displayStatus = 'Completed';
                    } else if (task.status === 'assigned' || task.status === 'in_progress') {
                        displayStatus = 'Incomplete';
                    } else {
                        displayStatus = task.status;
                    }

                    tr.innerHTML = `
                        <td><img src="../uploads/${task.profileImage || 'default.png'}" alt="Profile" class="profile-img"></td>
                        <td>${task.studentName}</td>
                        <td>${task.title}</td>
                        <td>${task.type}</td>
                        <td><div class="assessment ${displayStatus.toLowerCase()}">${displayStatus}</div></td>
                        <td>${task.score} / ${task.max_score ?? '-'}</td>
                    `;

                    tbody.appendChild(tr);
                });
            }
            function updateStudentTable() { renderStudentTaskTable(studentTasks); }

            // ================== FILTERS ==================
            const filterSelect = document.getElementById('taskTypeFilter');
            const statusFilter = document.getElementById('statusFilter');
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const suggestionsBox = document.getElementById('suggestions');
            const searchMessage = document.getElementById('searchMessage');

            function initFilters() {
                if (statusFilter) {
                    statusFilter.addEventListener('change', () => {
                        selectedStatus = statusFilter.value;
                        updateStudentTable();
                    });
                }

                if (filterSelect) {
                    filterSelect.addEventListener('change', () => {
                        if (filterSelect.value === 'all') {
                            selectedType = 'all';
                            selectedTitle = 'all';
                        } else {
                            const parsed = JSON.parse(filterSelect.value);
                            selectedType = parsed.type;
                            selectedTitle = parsed.title;
                        }
                        updateDonutChart(selectedType, searchQuery);
                        updateTabsOpenBarChart(selectedType, searchQuery);
                        updateScoreChart(selectedType, searchQuery);
                        updateStudentTable();
                    });
                }

                if (searchButton && searchInput && searchMessage) {
                    searchButton.addEventListener('click', () => {
                        searchQuery = searchInput.value.trim();
                        if (searchQuery === '') {
                            searchMessage.innerText = 'Please enter a student name.';
                        } else {
                            const hasResults = studentTasks.some(t => t.studentName && t.studentName.toLowerCase().includes(searchQuery.toLowerCase()));
                            searchMessage.innerText = hasResults ? '' : `No student found with name "${searchQuery}".`;
                        }
                        updateStudentTable();
                        updateDonutChart(selectedType, searchQuery);
                        updateTabsOpenBarChart(selectedType, searchQuery);
                        updateScoreChart(selectedType, searchQuery);
                        updateProgressCharts();
                    });
                }

                if (searchInput) {
                    searchInput.addEventListener('input', () => {
                        const query = searchInput.value.toLowerCase();
                        if (suggestionsBox) suggestionsBox.innerHTML = '';
                        if (query.length < 1) {
                            // reset
                            searchQuery = '';
                            if (searchMessage) searchMessage.innerText = '';
                            if (suggestionsBox) suggestionsBox.innerHTML = '';
                            updateStudentTable();
                            updateDonutChart(selectedType, searchQuery);
                            updateTabsOpenBarChart(selectedType, searchQuery);
                            updateScoreChart(selectedType, searchQuery);
                            updateProgressCharts();
                            return;
                        }

                        const uniqueNames = [...new Set(studentTasks.map(t => t.studentName).filter(Boolean))];
                        const filteredNames = uniqueNames.filter(name => name.toLowerCase().includes(query));

                        filteredNames.forEach(name => {
                            const div = document.createElement('div');
                            div.textContent = name;
                            div.addEventListener('click', () => {
                                searchInput.value = name;
                                if (suggestionsBox) suggestionsBox.innerHTML = '';
                                searchQuery = name;
                                updateStudentTable();
                                updateDonutChart(selectedType, searchQuery);
                                updateTabsOpenBarChart(selectedType, searchQuery);
                                updateScoreChart(selectedType, searchQuery);
                                updateProgressCharts();
                            });
                            if (suggestionsBox) suggestionsBox.appendChild(div);
                        });
                    });
                }

                document.addEventListener('click', e => {
                    if (searchInput && suggestionsBox) {
                        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                            suggestionsBox.innerHTML = '';
                        }
                    }
                });
            }

            // ================== INIT ==================
            if (filterSelect) {
                const defaultOption = document.createElement('option');
                defaultOption.value = 'all';
                defaultOption.textContent = 'All assessments';
                filterSelect.appendChild(defaultOption);
            }

            function addGroup(label, tasks, type) {
                if (!filterSelect || !tasks || tasks.length === 0) return;
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
            addGroup('Quizzes', data.taskBreakdown.quiz || [], 'quiz');
            addGroup('Activities', data.taskBreakdown.activity || [], 'activity');
            addGroup('Assignments', data.taskBreakdown.assignment || [], 'assignment');

            // initial draws
            updateScoreChart('all');
            updateDonutChart('all');
            updateTabsOpenBarChart('all');
            updateStudentTable();
            initFilters();
            updateProgressCharts();
        })
        .catch(err => console.error('Fetch error:', err));
}