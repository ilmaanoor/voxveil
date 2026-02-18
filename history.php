<?php
require_once 'php/session.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial=1.0">
    <title>VoxVeil - Session History</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
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

    <div id="session-modal" class="modal" style="display: none;">
        <div class="modal-content glass-card fade-in">
            <div class="flex-between mb-3">
                <h2 id="modal-title" class="text-gradient">Session Details</h2>
                <span class="close-modal" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
            </div>
            <div id="modal-body">
                <!-- Data will be injected here -->
            </div>
        </div>
    </div>

    <style>
    <style>
        .history-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .history-card {
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .history-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .history-metrics {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .metric-box {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 0.75rem;
            text-align: center;
        }

        .metric-box strong {
            display: block;
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
            color: var(--primary-light);
        }

        .metric-box span {
            font-size: 0.85rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }

        /* Modal Responsiveness */
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            backdrop-filter: blur(8px);
        }
        .modal-content {
            max-width: 900px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            padding: 3rem;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .transcript-pre {
            background: rgba(0,0,0,0.4);
            padding: 1.5rem;
            border-radius: 0.75rem;
            white-space: pre-wrap;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            line-height: 1.7;
            border: 1px solid rgba(255,255,255,0.05);
            margin-top: 1rem;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            .history-grid {
                grid-template-columns: 1fr;
            }
            .modal-content {
                padding: 2rem 1.5rem;
                max-height: 90vh;
            }
            .history-metrics.modal-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            .flex-between.modal-footer {
                flex-direction: column;
                gap: 1rem;
            }
            .flex-between.modal-footer .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .history-metrics {
                grid-template-columns: repeat(2, 1fr);
            }
            .metric-box strong {
                font-size: 1.25rem;
            }
        }
    </style>

    <script src="js/validation.js"></script>
    <script>
        let allSessions = [];

        $(document).ready(function() {
            loadHistory();
            
            // Close modal events
            $('.close-modal').on('click', () => $('#session-modal').fadeOut());
            $(window).on('click', (e) => {
                if ($(e.target).is('#session-modal')) $('#session-modal').fadeOut();
            });
        });

        function loadHistory() {
            $.get('php/practice-handler.php?action=get_stats', function(response) {
                if (response.success && response.recent_sessions) {
                    allSessions = response.recent_sessions;
                    displayHistory(allSessions);
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
                                <div class="history-date">${date.toLocaleDateString()}</div>
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
                                <strong>${session.questions_answered || 0}</strong>
                                <span>Questions</span>
                            </div>
                            <div class="metric-box">
                                <strong>${Math.round(session.duration / 60)}m</strong>
                                <span>Duration</span>
                            </div>
                        </div>

                        <div class="history-actions">
                            <button class="btn btn-primary w-full view-details-btn" data-index="${index}">
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
                const index = $(this).data('index');
                showSessionDetails(allSessions[index], sessions.length - index);
            });
        }

        function showSessionDetails(session, sessionNum) {
            const date = new Date(session.session_date);
            const score = session.confidence_score || 0;
            const scoreClass = score >= 80 ? 'success' : score >= 60 ? 'warning' : 'error';

            let modalHtml = `
                <div class="mb-4">
                    <p class="text-muted" style="font-size: 0.9rem;">${date.toLocaleDateString()} at ${date.toLocaleTimeString()}</p>
                </div>

                <div class="history-metrics modal-grid mb-4" style="grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                    <div class="metric-box">
                        <strong class="${scoreClass}">${score}%</strong>
                        <span>Score</span>
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
                        <strong>${session.questions_answered || 0}</strong>
                        <span>Questions</span>
                    </div>
                </div>

                ${session.feedback ? `
                    <div class="alert alert-info mb-4" style="background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.1); padding: 1.5rem;">
                        <h4 class="mb-2" style="color: var(--primary-light); display: flex; align-items: center; gap: 0.5rem;">
                            <span>💡</span> AI Feedback & Remarks
                        </h4>
                        <p style="line-height: 1.6;">${session.feedback}</p>
                    </div>
                ` : ''}

                <div class="history-transcript">
                    <h4 class="mb-2" style="display: flex; align-items: center; gap: 0.5rem;">
                        <span>📜</span> Full Interview Transcript
                    </h4>
                    <pre class="transcript-pre">${session.transcript || 'No transcript available for this session.'}</pre>
                </div>
                
                <div class="mt-5 flex-between modal-footer">
                    <a href="practice.php" class="btn btn-success" style="padding: 0.8rem 2rem;">🔄 Retake This Session</a>
                    <button class="btn btn-secondary close-modal-btn" style="padding: 0.8rem 2rem;">Close Details</button>
                </div>
            `;

            $('#modal-title').text(`Session #${sessionNum} Details`);
            $('#modal-body').html(modalHtml);
            $('#session-modal').fadeIn();

            $('.close-modal-btn').on('click', () => $('#session-modal').fadeOut());
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
