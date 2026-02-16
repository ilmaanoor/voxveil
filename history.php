<?php
require_once 'php/session.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoxVeil - Session History</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">VoxVeil</div>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="form.php">Get Started</a></li>
                <li><a href="practice.php">Practice</a></li>
                <li><a href="progress.php">Progress</a></li>
                <li><a href="history.php" class="active">History</a></li>
            </ul>
        </nav>
    </header>

    <div class="container" style="padding: 3rem 0;">
        <h1 class="text-center text-gradient">Session History</h1>
        <p class="text-center" style="color: var(--text-secondary); margin-bottom: 3rem;">
            Review your past interview sessions and retake them to track improvement
        </p>

        <div id="history-container"></div>
    </div>

    <footer>
        <div class="container">
            <p class="text-center">&copy; 2026 VoxVeil. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .history-grid {
            display: grid;
            gap: 1.5rem;
        }

        .history-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .history-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-glow);
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .history-title h3 {
            margin-bottom: 0.5rem;
        }

        .history-date {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .history-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-excellent {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .badge-good {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .badge-needs-work {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .history-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .metric-box {
            background: var(--bg-secondary);
            padding: 1rem;
            border-radius: var(--radius-md);
            text-align: center;
        }

        .metric-box strong {
            display: block;
            font-size: 1.5rem;
            color: var(--primary-light);
            margin-bottom: 0.25rem;
        }

        .metric-box span {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .history-transcript {
            background: var(--bg-secondary);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            max-height: 200px;
            overflow-y: auto;
        }

        .history-transcript h4 {
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .history-transcript pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: 'Inter', sans-serif;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .history-actions {
            display: flex;
            gap: 1rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .history-header {
                flex-direction: column;
                gap: 1rem;
            }
            .history-actions {
                flex-direction: column;
            }
        }
    </style>

    <script src="js/validation.js"></script>
    <script>
        $(document).ready(function() {
            loadHistory();
        });

        function loadHistory() {
            $.get('php/practice-handler.php?action=get_stats', function(response) {
                if (response.success && response.recent_sessions) {
                    displayHistory(response.recent_sessions);
                } else {
                    showEmptyState();
                }
            }, 'json').fail(function() {
                showAlert('Error loading history', 'error');
            });
        }

        function displayHistory(sessions) {
            if (!sessions || sessions.length === 0) {
                showEmptyState();
                return;
            }

            let html = '<div class="history-grid">';
            
            sessions.forEach((session, index) => {
                const date = new Date(session.session_date);
                const score = session.confidence_score || 0;
                const badge = score >= 80 ? 'excellent' : score >= 60 ? 'good' : 'needs-work';
                const badgeText = score >= 80 ? '🌟 Excellent' : score >= 60 ? '👍 Good' : '💪 Needs Work';
                
                html += `
                    <div class="history-card fade-in" style="animation-delay: ${index * 0.1}s">
                        <div class="history-header">
                            <div class="history-title">
                                <h3>Session #${sessions.length - index}</h3>
                                <div class="history-date">${date.toLocaleDateString()} at ${date.toLocaleTimeString()}</div>
                            </div>
                            <div class="history-badge badge-${badge}">${badgeText}</div>
                        </div>

                        <div class="history-metrics">
                            <div class="metric-box">
                                <strong>${score}%</strong>
                                <span>Confidence</span>
                            </div>
                            <div class="metric-box">
                                <strong>${session.words_per_minute || 0}</strong>
                                <span>WPM</span>
                            </div>
                            <div class="metric-box">
                                <strong>${session.filler_count || 0}</strong>
                                <span>Fillers</span>
                            </div>
                            <div class="metric-box">
                                <strong>${Math.round(session.duration / 60)}</strong>
                                <span>Minutes</span>
                            </div>
                        </div>

                        ${session.transcript ? `
                            <div class="history-transcript">
                                <h4>Session Transcript</h4>
                                <pre>${session.transcript.substring(0, 300)}${session.transcript.length > 300 ? '...' : ''}</pre>
                            </div>
                        ` : ''}

                        ${session.feedback ? `
                            <div style="background: rgba(99, 102, 241, 0.1); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                                <strong>💡 Feedback:</strong> ${session.feedback}
                            </div>
                        ` : ''}

                        <div class="history-actions">
                            <a href="practice.php" class="btn btn-primary">🔄 Retake Interview</a>
                            <button class="btn btn-secondary view-details-btn" data-session-id="${session.id}">
                                📄 View Full Details
                            </button>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            $('#history-container').html(html);

            // View details button handler
            $('.view-details-btn').on('click', function() {
                const sessionId = $(this).data('session-id');
                alert('Full session details feature coming soon!');
            });
        }

        function showEmptyState() {
            $('#history-container').html(`
                <div class="empty-state glass-card">
                    <div class="empty-state-icon">📋</div>
                    <h3>No Sessions Yet</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                        Start practicing to build your session history
                    </p>
                    <a href="practice.php" class="btn btn-primary">Start Your First Session</a>
                </div>
            `);
        }
    </script>
</body>
</html>
