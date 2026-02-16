<?php
require_once 'php/session.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoxVeil - Practice Interview</title>
    <link rel="stylesheet" href="css/styles.css">
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

            <div id="alert-container"></div>

            <div class="practice-grid">
                <!-- Left Side - Input Controls -->
                <section class="practice-controls glass-card">
                    <h3>Input Method</h3>
                    
                    <div class="input-methods">
                        <!-- Voice Input -->
                        <div class="input-method-card">
                            <h4>🎤 Voice Input</h4>
                            <p>Speak your answer and we'll transcribe it in real-time</p>
                            <button id="mic-btn" class="btn btn-primary w-full">
                                🎤 Start Speaking
                            </button>
                            <div id="mic-icon" class="mic-visual"></div>
                        </div>

                        <div class="divider">OR</div>

                        <!-- Text Input -->
                        <div class="input-method-card">
                            <h4>⌨️ Type Your Answer</h4>
                            <p>Prefer typing? Enter your response below</p>
                            <textarea 
                                id="text-input" 
                                class="form-control" 
                                placeholder="Type your answer here..."
                                rows="4"></textarea>
                            <button id="submit-answer-btn" class="btn btn-success w-full">
                                Submit Answer
                            </button>
                        </div>
                    </div>

                    <!-- Session Metrics -->
                    <div class="metrics-panel">
                        <h4>Session Metrics</h4>
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
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button id="next-question-btn" class="btn btn-secondary">
                            Next Question
                        </button>
                        <button id="end-session-btn" class="btn btn-danger">
                            End Session
                        </button>
                    </div>
                </section>

                <!-- Right Side - Question & Transcript -->
                <aside class="practice-display">
                    <!-- Question Display -->
                    <div class="question-panel glass-card">
                        <div id="question-display">
                            <div class="question-card fade-in">
                                <h4>Interview Question</h4>
                                <p>Loading question...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Transcript Display -->
                    <div class="transcript-panel glass-card">
                        <h3>Your Response Transcript</h3>
                        <div id="transcript-display" class="transcript-content">
                            <p class="text-muted">Your answer will appear here...</p>
                        </div>
                    </div>
                </aside>
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

        .practice-subtitle {
            color: var(--text-secondary);
            margin-bottom: var(--spacing-xl);
        }

        .practice-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: var(--spacing-lg);
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

        .input-method-card h4 {
            margin-bottom: 0.5rem;
        }

        .input-method-card p {
            font-size: 0.9rem;
            margin-bottom: var(--spacing-sm);
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

        .recording-pulse {
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            50% {
                box-shadow: 0 0 0 20px rgba(239, 68, 68, 0);
            }
        }

        .metrics-panel {
            padding: var(--spacing-md);
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-md);
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
            font-size: 2rem;
            font-weight: 700;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .metric-label {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .action-buttons {
            display: flex;
            gap: var(--spacing-sm);
        }

        .action-buttons button {
            flex: 1;
        }

        .question-panel {
            margin-bottom: var(--spacing-md);
        }

        .question-card {
            padding: var(--spacing-md);
            background: var(--gradient-1);
            border-radius: var(--radius-md);
        }

        .question-card h4 {
            color: white;
            margin-bottom: 0.5rem;
        }

        .question-card p {
            color: white;
            font-size: 1.125rem;
            margin: 0;
        }

        .transcript-panel {
            min-height: 400px;
        }

        .transcript-content {
            background: var(--bg-secondary);
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            min-height: 300px;
            max-height: 500px;
            overflow-y: auto;
        }

        .filler-highlight {
            background: rgba(239, 68, 68, 0.3);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            color: var(--error);
            font-weight: 600;
        }

        .w-full {
            width: 100%;
        }

        @media (max-width: 968px) {
            .practice-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }
        }
    </style>

    <script src="js/validation.js"></script>
    <script src="js/practice.js"></script>
</body>
</html>
