// progress.js - Progress Analytics and Visualization
// Uses Chart.js for data visualization

let progressChart;
let trendsChart;

$(document).ready(function () {
    loadProgressData();

});

function loadProgressData() {
    $.get('php/practice-handler.php?action=get_stats', function (response) {
        if (response.success) {
            updateStatCards(response.stats);
            displayRecentSessions(response.recent_sessions);
            createProgressCharts(response.recent_sessions);
        } else {
            showAlert('Failed to load progress data', 'error');
        }
    }, 'json').fail(function () {
        showAlert('Error loading progress data', 'error');
    });
}

function updateStatCards(stats) {
    $('#total-sessions').text(stats.total_sessions || 0);
    $('#avg-confidence').text(Math.round(stats.avg_confidence || 0) + '%');
    $('#avg-wpm').text(Math.round(stats.avg_wpm || 0));

    const totalMinutes = Math.round((stats.total_practice_time || 0) / 60);
    $('#total-practice-time').text(totalMinutes);
}

function displayRecentSessions(sessions) {
    if (!sessions || sessions.length === 0) {
        $('#recent-sessions-list').html('<p class="text-muted">No sessions yet. Start practicing!</p>');
        return;
    }

    let html = '';
    sessions.forEach((session, index) => {
        // MySQL stores datetime in UTC — append ' UTC' so JavaScript converts to local time correctly
        const rawDate = session.session_date || '';
        const dateObj = new Date(rawDate.includes('Z') || rawDate.includes('+') ? rawDate : rawDate + ' UTC');
        const score = session.confidence_score || 0;
        const scoreClass = score >= 80 ? 'success' : score >= 60 ? 'warning' : 'error';

        const displayDate = dateObj.toLocaleDateString();
        const displayTime = dateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        html += `
            <div class="session-item fade-in" style="animation-delay: ${index * 0.1}s">
                <div class="session-card-header flex-between mb-2">
                    <div>
                        <span class="session-date">${displayDate}</span>
                        <span class="session-time text-muted" style="font-size: 0.8rem; margin-left: 0.5rem;">
                            ${displayTime}
                        </span>
                    </div>
                    <span class="confidence-badge badge-${scoreClass}">${Math.round(session.confidence_score)}%</span>
                </div>
                <div class="session-stats">
                    <div class="stat-chip">
                        <span class="stat-label">Score:</span>
                        <span class="stat-value ${scoreClass}">${score}%</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">WPM:</span>
                        <span class="stat-value">${session.words_per_minute || 0}</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Fillers:</span>
                        <span class="stat-value">${session.filler_count || 0}</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Duration:</span>
                        <span class="stat-value">${Math.round(session.duration / 60)}m</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Questions:</span>
                        <span class="stat-value">${session.questions_answered || 0}</span>
                    </div>
                </div>
                ${session.feedback ? `<p class="session-feedback">💡 ${session.feedback}</p>` : ''}
            </div>
        `;
    });

    $('#recent-sessions-list').html(html);
}

function createProgressCharts(sessions) {
    if (!sessions || sessions.length === 0) return;

    // Prepare data
    const sessionsCopy = [...sessions].reverse();
    const labels = sessionsCopy.map((s, i) => `Session ${i + 1}`);
    const confidenceData = sessionsCopy.map(s => s.confidence_score || 0);
    const wpmData = sessionsCopy.map(s => s.words_per_minute || 0);
    const fillerData = sessionsCopy.map(s => s.filler_count || 0);

    // Progress Over Time Chart
    const progressCtx = document.getElementById('progress-chart');
    if (progressCtx) {
        if (progressChart) progressChart.destroy();

        progressChart = new Chart(progressCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Confidence Score',
                    data: confidenceData,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#334155' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { color: '#334155' },
                        grid: { color: 'rgba(51, 65, 85, 0.1)' }
                    },
                    x: {
                        ticks: { color: '#334155' },
                        grid: { color: 'rgba(51, 65, 85, 0.1)' }
                    }
                }
            }
        });
    }

    // Multi-Metric Chart
    const trendsCtx = document.getElementById('trends-chart');
    if (trendsCtx) {
        if (trendsChart) trendsChart.destroy();

        trendsChart = new Chart(trendsCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'WPM',
                        data: wpmData,
                        backgroundColor: 'rgba(20, 184, 166, 0.7)',
                        borderColor: '#14b8a6',
                        borderWidth: 2,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Filler Words',
                        data: fillerData,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: '#ef4444',
                        borderWidth: 2,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#334155' }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'WPM',
                            color: '#14b8a6'
                        },
                        ticks: { color: '#334155' },
                        grid: { color: 'rgba(51, 65, 85, 0.1)' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Filler Count',
                            color: '#ef4444'
                        },
                        grid: {
                            drawOnChartArea: false // only want the grid lines for one axis
                        },
                        ticks: { color: '#334155' }
                    },
                    x: {
                        ticks: { color: '#334155' },
                        grid: { color: 'rgba(51, 65, 85, 0.1)' }
                    }
                }
            }
        });
    }
}
