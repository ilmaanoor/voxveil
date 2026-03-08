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

        <div class="container py-3">
        <h1 class="text-center text-gradient">Session History</h1>
        <p class="text-center mb-3">
            Review your past interview sessions and retake them to track improvement
        </p>

        <div id="history-container"></div>
    </div>

    <div id="session-modal" class="modal hidden">
        <div class="modal-content clay-card fade-in">
            <div class="flex-between mb-3">
                <h2 id="modal-title" class="text-gradient">Session Details</h2>
                <span class="close-modal">&times;</span>
            </div>
            <div id="modal-body">
                <!-- Data will be injected here -->
            </div>
        </div>
    </div>



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
                    <p class="text-muted text-tiny">${date.toLocaleDateString()} at ${date.toLocaleTimeString()}</p>
                </div>

                <div class="history-metrics history-metrics-grid mb-4">
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
                    <div class="alert alert-info mb-4 feedback-alert">
                        <h4 class="mb-2 feedback-header flex-gap-1">
                            <span>💡</span> AI Feedback & Remarks
                        </h4>
                        <p class="line-height-base">${session.feedback}</p>
                    </div>
                ` : ''}

                <div class="history-transcript">
                    <h4 class="mb-2 flex-gap-1">
                        <span>📜</span> Full Interview Transcript
                    </h4>
                    <pre class="transcript-pre">${session.transcript || 'No transcript available for this session.'}</pre>
                </div>
                
                <div class="mt-5 flex-between modal-footer">
                    <a href="practice.php" class="btn btn-success p-btn">🔄 Retake This Session</a>
                    <button class="btn btn-secondary close-modal-btn p-btn">Close Details</button>
                </div>
            `;

            $('#modal-title').text(`Session #${sessionNum} Details`);
            $('#modal-body').html(modalHtml);
            $('#session-modal').fadeIn();

            $('.close-modal-btn').on('click', () => $('#session-modal').fadeOut());
        }

        function showEmptyState() {
            $('#history-container').html(`
                <div class="empty-state clay-card">
                    <div class="empty-state-icon">📋</div>
                    <h3>No Sessions Yet</h3>
                    <p class="text-secondary mb-2">
                        Start practicing to build your session history
                    </p>
                    <a href="practice.php" class="btn btn-primary">Start Your First Session</a>
                </div>
            `);
        }
    </script>
</body>
</html>
