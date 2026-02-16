<?php
require_once 'php/session.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoxVeil - Your Progress</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">VoxVeil</div>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="form.php">Get Started</a></li>
                <li><a href="practice.php">Practice</a></li>
                <li><a href="progress.php" class="active">Progress</a></li>
                <li><a href="history.php">History</a></li>
            </ul>
        </nav>
    </header>

    <div class="container" style="padding: 3rem 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 class="text-gradient">Your Progress</h1>
            <button id="refresh-stats-btn" class="btn btn-secondary">🔄 Refresh</button>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card glass-card">
                <div class="stat-icon">📊</div>
                <h3 id="total-sessions">0</h3>
                <p>Total Sessions</p>
            </div>
            <div class="stat-card glass-card">
                <div class="stat-icon">💯</div>
                <h3 id="avg-confidence">0%</h3>
                <p>Avg Confidence</p>
            </div>
            <div class="stat-card glass-card">
                <div class="stat-icon">⚡</div>
                <h3 id="avg-wpm">0</h3>
                <p>Avg WPM</p>
            </div>
            <div class="stat-card glass-card">
                <div class="stat-icon">⏱️</div>
                <h3 id="total-practice-time">0</h3>
                <p>Minutes Practiced</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-section">
            <div class="chart-container glass-card">
                <h3>Confidence Progress Over Time</h3>
                <canvas id="progress-chart"></canvas>
            </div>
            <div class="chart-container glass-card">
                <h3>Performance Metrics</h3>
                <canvas id="trends-chart"></canvas>
            </div>
        </div>

        <!-- Recent Sessions -->
        <div class="recent-sessions glass-card">
            <h3>Recent Sessions</h3>
            <div id="recent-sessions-list"></div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p class="text-center">&copy; 2026 VoxVeil. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
        }

        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .stat-card h3 {
            font-size: 2.5rem;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .charts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .chart-container {
            padding: 2rem;
        }

        .chart-container h3 {
            margin-bottom: 1.5rem;
        }

        .chart-container canvas {
            max-height: 300px;
        }

        .recent-sessions {
            padding: 2rem;
        }

        .session-item {
            background: var(--bg-secondary);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 1rem;
        }

        .session-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .session-date {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .session-stats {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 0.5rem;
        }

        .stat-chip {
            background: var(--bg-tertiary);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .stat-label {
            color: var(--text-muted);
        }

        .stat-value {
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .stat-value.success { color: var(--success); }
        .stat-value.warning { color: var(--warning); }
        .stat-value.error { color: var(--error); }

        .session-feedback {
            font-style: italic;
            color: var(--text-secondary);
            margin-top: 1rem;
        }

        .rotating {
            animation: rotate 1s linear;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .charts-section {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script src="js/validation.js"></script>
    <script src="js/progress.js"></script>
</body>
</html>
