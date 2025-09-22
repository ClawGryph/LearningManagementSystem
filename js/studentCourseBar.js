function initScoreBar() {
    let scoreChart, donutChart, barChart;
    const progressDonutCharts = {};

    let selectedType = 'all';
    let selectedTitle = 'all';
    let selectedStatus = 'all';
    let searchQuery = '';

    fetch('../action/get-task-statistics.php')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error loading data:', data.error);
                return;
            }

            // ====== DOM refs ======
            const courseTitleElem = document.getElementById("courseTitle");
            const filterSelect = document.getElementById('taskTypeFilter');
            const statusFilter = document.getElementById('statusFilter');
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const suggestionsBox = document.getElementById('suggestions');
            const searchMessage = document.getElementById('searchMessage');

            const studentTasks = data.studentTaskStatus || [];

            // ====== Utility: refresh all charts/tables ======
            function refreshAll() {
                updateScoreChart(selectedType, searchQuery);
                updateDonutChart(selectedType, searchQuery);
                updateTabsOpenBarChart(selectedType, searchQuery);
                updateStudentTable();
                updateProgressCharts();
            }

            // ====== SCORE BAR CHART ======
            function updateScoreChart(filter = 'all', search = '') {
                const scoresByType = data.studentScoresByType || {};
                let studentScoreData = filter === 'all'
                    ? Object.values(scoresByType).flat()
                    : scoresByType[filter] || [];

                // --- remove duplicates by student name ---
                const uniqueMap = {};
                studentScoreData.forEach(s => {
                    uniqueMap[s.name] = s; // overwrite duplicates
                });
                studentScoreData = Object.values(uniqueMap);

                const filtered = search
                    ? studentScoreData.filter(s => s.name?.toLowerCase().includes(search.toLowerCase()))
                    : studentScoreData;

                const labels = filtered.map(s => s.name);
                const scores = filtered.map(s => s.score);

                const scoreCanvas = document.getElementById('scoreChart');
                if (!scoreCanvas) return;
                const ctx = scoreCanvas.getContext('2d');

                if (scoreChart) scoreChart.destroy();
                scoreChart = new Chart(ctx, {
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
                    options: { responsive: true, scales: { y: { beginAtZero: true, max: 100 } } }
                });
            }

            // ====== PROGRESS DONUTS ======
            function createDonutChart(canvasId, completed, total) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                if (progressDonutCharts[canvasId]) progressDonutCharts[canvasId].destroy();

                const percent = total > 0 ? Math.round((completed / total) * 100) : 0;
                let arcColor = '#e53935';
                if (percent > 50) arcColor = '#4caf50';
                else if (percent >= 30) arcColor = '#ff9800';

                const centerText = {
                    id: 'centerText',
                    beforeDraw(chart) {
                        const { width, height, ctx } = chart;
                        ctx.save();
                        ctx.font = 'bold 18px Arial';
                        ctx.fillStyle = arcColor;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(percent + '%', width / 2, height / 2 - 8);
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
                        datasets: [{ data: [completed, total - completed], backgroundColor: [arcColor, '#e0e0e0'] }]
                    },
                    options: { cutout: '70%', plugins: { legend: { display: false } } },
                    plugins: [centerText]
                });
            }

            function updateProgressCharts() {
                const filteredTasks = studentTasks.filter(t => {
                    if (selectedType !== 'all' && t.type !== selectedType) return false;
                    if (selectedTitle !== 'all' && t.title !== selectedTitle) return false;
                    if (selectedStatus !== 'all' && (statusMap[t.status] || 'incomplete') !== selectedStatus) return false;
                    if (searchQuery && !t.studentName?.toLowerCase().includes(searchQuery.toLowerCase())) return false;
                    return true;
                });

                // Count by type + status
                const breakdown = { quiz: { completed:0, incomplete:0, late:0 },
                                    assignment: { completed:0, incomplete:0, late:0 },
                                    activity: { completed:0, incomplete:0, late:0 } };

                filteredTasks.forEach(t => {
                    const type = t.type;
                    const mapped = statusMap[t.status] || 'incomplete';
                    if (breakdown[type]) breakdown[type][mapped]++;
                });

                // Update each donut
                ['quiz','assignment','activity'].forEach(type => {
                    const b = breakdown[type];
                    const total = b.completed + b.incomplete + b.late;
                    createDonutChart(type + "Chart", b.completed, total);
                });
            }

            // ====== TASK TYPE DONUT ======
            function updateDonutChart(filter = 'all', search = '') {
                const taskTypeCanvas = document.getElementById('taskTypeDonutChart');
                if (!taskTypeCanvas) return;
                const ctx = taskTypeCanvas.getContext('2d');
                if (donutChart) donutChart.destroy();

                const relevantTasks = studentTasks.filter(t =>
                    (!search || t.studentName?.toLowerCase().includes(search.toLowerCase())) &&
                    (filter === 'all' || t.type === filter)
                );

                const counts = {};
                relevantTasks.forEach(t => { counts[t.title] = (counts[t.title] || 0) + 1; });

                const labels = Object.keys(counts).length ? Object.keys(counts) : ['No tasks yet'];
                const dataArr = Object.keys(counts).length ? Object.values(counts) : [1];

                donutChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: { labels, datasets: [{ data: dataArr, backgroundColor: ['#4CAF50', '#FFC107', '#FF5722'] }] },
                    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                });
            }

            // ====== TABS OPEN BAR ======
            function updateTabsOpenBarChart(filter = 'all', search = '') {
                const tabsCanvas = document.getElementById('tabsOpenBarChart');
                if (!tabsCanvas) return;
                const ctx = tabsCanvas.getContext('2d');
                if (barChart) barChart.destroy();

                // Filter per student
                let relevantTasks = studentTasks.filter(t => {
                    if (search && !t.studentName?.toLowerCase().includes(search.toLowerCase())) return false;
                    if (filter !== 'all' && t.type !== filter) return false;
                    return true;
                });

                let labels = [];
                let dataArr = [];

                if (search) {
                    // ✅ Show per-student values directly
                    labels = relevantTasks.map(t => t.title);
                    dataArr = relevantTasks.map(t => t.tabs_open ?? 0);
                } else {
                    // ✅ No student searched → sum tabs_open per assessment
                    const grouped = {};
                    relevantTasks.forEach(t => {
                        if (!grouped[t.title]) grouped[t.title] = 0;
                        grouped[t.title] += (t.tabs_open ?? 0);
                    });
                    labels = Object.keys(grouped);
                    dataArr = Object.values(grouped);
                }

                barChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Tabs Opened',
                            data: dataArr,
                            backgroundColor: '#42A5F5'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // ====== TASK TABLE ======
            const statusMap = { submitted: 'completed', graded: 'completed', assigned: 'incomplete', in_progress: 'incomplete', late: 'late' };

            function updateStudentTable() {
                const tbody = document.querySelector('#studentTaskTable tbody');
                if (!tbody) return;
                tbody.innerHTML = '';

                const filtered = studentTasks.filter(t => {
                    let mappedStatus = statusMap[t.status] || 'incomplete';
                    if (selectedStatus !== 'all' && mappedStatus !== selectedStatus) return false;
                    if (selectedType !== 'all' && t.type !== selectedType) return false;
                    if (selectedTitle !== 'all' && t.title !== selectedTitle) return false;
                    if (searchQuery && !t.studentName?.toLowerCase().includes(searchQuery.toLowerCase())) return false;
                    return true;
                });

                if (!filtered.length) {
                    tbody.innerHTML = '<tr><td colspan="6">No matching records</td></tr>';
                    return;
                }

                filtered.forEach(t => {
                    const displayStatus = t.status === 'late' ? 'Late' :
                                          (['submitted','graded'].includes(t.status) ? 'Completed' : 'Incomplete');

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><img src="../uploads/${t.profileImage || 'default.png'}" class="profile-img"></td>
                        <td>${t.studentName}</td>
                        <td>${t.title}</td>
                        <td>${t.type}</td>
                        <td><div class="assessment ${displayStatus.toLowerCase()}">${displayStatus}</div></td>
                        <td>${t.score} / ${t.max_score ?? '-'}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            // ====== FILTERS ======
            function initFilters() {
                if (statusFilter) {
                    statusFilter.addEventListener('change', () => {
                        selectedStatus = statusFilter.value;
                        refreshAll();
                    });
                }

                if (filterSelect) {
                    filterSelect.innerHTML = `<option value="all">All assessments</option>`;
                    function addGroup(label, tasks, type) {
                        if (!tasks.length) return;
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

                    filterSelect.addEventListener('change', () => {
                        if (filterSelect.value === 'all') {
                            selectedType = 'all'; selectedTitle = 'all';
                        } else {
                            const parsed = JSON.parse(filterSelect.value);
                            selectedType = parsed.type; selectedTitle = parsed.title;
                        }
                        refreshAll();
                    });
                }

                if (searchButton && searchInput) {
                    searchButton.addEventListener('click', () => {
                        searchQuery = searchInput.value.trim();
                        if (!searchQuery) {
                            searchMessage.innerText = 'Please enter a student name.';
                        } else {
                            const hasResults = studentTasks.some(t => t.studentName?.toLowerCase().includes(searchQuery.toLowerCase()));
                            searchMessage.innerText = hasResults ? '' : `No student found with name "${searchQuery}".`;
                        }
                        refreshAll();
                    });
                }

                if (searchInput) {
                    searchInput.addEventListener('input', () => {
                        const q = searchInput.value.toLowerCase();
                        suggestionsBox.innerHTML = '';
                        if (!q) { searchQuery = ''; searchMessage.innerText = ''; refreshAll(); return; }

                        const names = [...new Set(studentTasks.map(t => t.studentName).filter(Boolean))];
                        names.filter(n => n.toLowerCase().includes(q)).forEach(name => {
                            const div = document.createElement('div');
                            div.textContent = name;
                            div.addEventListener('click', () => {
                                searchInput.value = name;
                                suggestionsBox.innerHTML = '';
                                searchQuery = name;
                                refreshAll();
                            });
                            suggestionsBox.appendChild(div);
                        });
                    });

                    document.addEventListener('click', e => {
                        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                            suggestionsBox.innerHTML = '';
                        }
                    });
                }
            }

            // ====== INIT ======
            if (courseTitleElem) courseTitleElem.innerText = data.courseName || '';
            initFilters();
            refreshAll();
        })
        .catch(err => console.error('Fetch error:', err));
}