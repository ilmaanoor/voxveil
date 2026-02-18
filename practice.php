<?php
require_once 'php/session.php';
requireLogin();

require_once 'php/config.php';
require_once 'php/db-operations.php';
$db = new DatabaseOperations($conn);
$profile = $db->getUserProfile($_SESSION['user_id']);

if (!$profile) {
    header("Location: form.php?alert=complete_profile");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoxVeil - Practice Interview</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="container">
            <div class="logo">VoxVeil</div>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="form.php">Get Started</a></li>
                <li><a href="practice.php" class="active">Practice</a></li>
                <li><a href="progress.php">Progress</a></li>
                <li><a href="history.php">History</a></li>
            </ul>
        </nav>
    </header>

    <div class="practice-page-wrapper">
        <div class="container">
            <h1 class="text-center text-gradient">Practice Session</h1>
            <p class="text-center practice-subtitle">Choose your input method and start practicing</p>

            <div class="session-info-bar flex-between mb-2">
                <div class="info-pill">
                    <span class="pill-label">Question:</span>
                    <span id="question-counter" class="pill-value">0 / 14</span>
                </div>
                <div class="info-pill">
                    <span class="pill-label">Answers Saved:</span>
                    <span id="answers-saved" class="pill-value">0</span>
                </div>
                <div class="info-pill">
                    <span class="pill-label">Started:</span>
                    <span id="session-start-time" class="pill-value">Just now</span>
                </div>
            </div>

            <div id="alert-container"></div>

            <!-- Top Level: Question & Main Actions (Added) -->
            <div class="question-header-section glass-card mb-3">
                <div class="flex-between align-start">
                    <div id="question-display" class="flex-grow">
                        <div class="question-card fade-in">
                            <h4 class="text-muted mb-1">Current Interview Question</h4>
                            <p class="h3">Loading question...</p>
                        </div>
                    </div>
                    <button id="next-question-btn" class="btn btn-secondary pulse-primary">
                        Next Question ➔
                    </button>
                </div>
            </div>

            <div class="practice-grid">
                <!-- Row 1, Col 1: Voice Input -->
                <div class="input-method-card glass-card">
                    <div class="flex-between mb-2">
                        <h3>🎤 Voice Input</h3>
                    </div>
                    <p class="mb-2">Speak your answer and we'll transcribe it in real-time</p>
                    <button id="mic-btn" class="btn btn-primary w-full py-3">
                        🎤 Start Speaking
                    </button>
                    <div id="mic-icon" class="mic-visual"></div>
                </div>

                <!-- Row 1, Col 2: Response Transcript (Now next to Voice) -->
                <div class="transcript-panel glass-card">
                    <div class="flex-between mb-2">
                        <h3>Your Response Transcript</h3>
                        <div style="display: flex; gap: 0.5rem;">
                            <button id="redo-btn" class="btn btn-warning btn-sm" style="display: none;">
                                🔄 Redo / Delete
                            </button>
                        </div>
                    </div>
                    <div id="transcript-display" class="transcript-content">
                        <p class="text-muted">Your answer will appear here...</p>
                    </div>
                    <button id="submit-voice-btn" class="btn btn-success w-full mt-2">
                        Submit Voice Answer
                    </button>
                </div>

                <!-- Row 2, Col 1: Text Input -->
                <div class="input-method-card glass-card">
                    <div class="flex-between mb-2">
                        <h3>⌨️ Type Your Answer</h3>
                    </div>
                    <p class="mb-2">Prefer typing? Enter your response below</p>
                    <textarea 
                        id="text-input" 
                        class="form-control" 
                        placeholder="Type your answer here..."
                        rows="4"></textarea>
                    <button id="submit-answer-btn" class="btn btn-success w-full mt-2">
                        Submit Answer
                    </button>
                </div>

                <!-- Row 2, Col 2: Session Metrics -->
                <div class="practice-display glass-card">
                    <div class="flex-between mb-2">
                        <h3>Session Metrics</h3>
                    </div>
                    <div class="metrics-grid">
                        <div class="metric-item">
                            <div class="metric-value" id="filler-count">0</div>
                            <div class="metric-label">Filler Words</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-value" id="word-count">0</div>
                            <div class="metric-label">Total Words</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-value" id="wpm-display">0</div>
                            <div class="metric-label">Words/Min</div>
                        </div>
                        <div class="metric-item">
                            <div class="metric-value" id="confidence-score">0%</div>
                            <div class="metric-label">Confidence</div>
                        </div>
                    </div>
                    
                    <div class="action-buttons mt-3">
                        <button id="end-session-btn" class="btn btn-danger w-full">
                            End Session
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p class="text-center">&copy; 2026 VoxVeil. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .practice-page-wrapper {
            min-height: calc(100vh - 200px);
            padding: 2rem 0;
        }

        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--spacing-md);
        }

        .align-start {
            align-items: flex-start;
        }

        .flex-grow {
            flex-grow: 1;
        }

        .question-header-section {
            padding: var(--spacing-lg);
            background: var(--gradient-1);
            color: white;
            border: none;
        }

        .question-header-section .text-muted {
            color: rgba(255,255,255,0.7) !important;
        }

        .question-header-section p.h3 {
            font-size: 1.5rem;
            font-weight: 500;
            margin: 0;
            line-height: 1.4;
        }

        .pulse-primary {
            animation: pulse-small 2s infinite;
            background: white !important;
            color: var(--primary) !important;
            font-weight: 700;
        }

        @keyframes pulse-small {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }

        .practice-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-lg);
        }

        @media (max-width: 968px) {
            .practice-grid {
                grid-template-columns: 1fr;
            }
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }

        .mt-3 {
            margin-top: var(--spacing-lg);
        }

        .input-methods {
            margin-bottom: var(--spacing-lg);
        }

        .input-method-card {
            padding: var(--spacing-md);
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-md);
        }

        .divider {
            text-align: center;
            color: var(--text-muted);
            margin: var(--spacing-md) 0;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: var(--glass-border);
        }

        .divider::before { left: 0; }
        .divider::after { right: 0; }

        .mic-visual {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--bg-tertiary);
            margin: var(--spacing-md) auto 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: var(--spacing-md);
            margin-top: var(--spacing-md);
        }

        .metric-item {
            text-align: center;
            padding: var(--spacing-sm);
            background: var(--bg-tertiary);
            border-radius: var(--radius-sm);
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .action-buttons {
            display: flex;
            gap: var(--spacing-sm);
        }

        .action-buttons button {
            flex: 1;
        }

        .transcript-panel {
            min-height: 400px;
        }

        .transcript-content {
            background: var(--bg-secondary);
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>

    <script src="js/validation.js?v=<?php echo time(); ?>"></script>
    <script src="js/practice.js?v=<?php echo time(); ?>"></script>
</body>
</html>
