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
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
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

    <div class="container page-section">
        <div class="progress-header">
            <h1 class="text-gradient">Your Progress</h1>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card clay-card">
                <div class="stat-icon">📊</div>
                <h3 id="total-sessions">0</h3>
                <p>Total Sessions</p>
            </div>
            <div class="stat-card clay-card">
                <div class="stat-icon">💯</div>
                <h3 id="avg-confidence">0%</h3>
                <p>Avg Confidence</p>
            </div>
            <div class="stat-card clay-card">
                <div class="stat-icon">⚡</div>
                <h3 id="avg-wpm">0</h3>
                <p>Avg WPM</p>
            </div>
            <div class="stat-card clay-card">
                <div class="stat-icon">⏱️</div>
                <h3 id="total-practice-time">0</h3>
                <p>All-Time Practice (Min)</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-section">
            <div class="chart-container clay-card">
                <h3>Confidence Progress Over Time</h3>
                <canvas id="progress-chart"></canvas>
            </div>
            <div class="chart-container clay-card">
                <h3>Performance Metrics</h3>
                <canvas id="trends-chart"></canvas>
            </div>
        </div>

        <!-- Recent Sessions -->
        <div class="recent-sessions clay-card mb-4">
            <h3>Recent Sessions</h3>
            <div id="recent-sessions-list"></div>
        </div>

        <div class="text-center mt-4 mb-4">
            <a href="history.php" class="btn btn-primary" style="padding: 15px 30px; font-size: 1.1rem; text-decoration: none; border-radius: 8px; display: inline-block;">
                Go to Next Page (History) ➔
            </a>
        </div>
    </div>

    <footer>
        <div class="container">
            <p class="text-center">&copy; 2026 VoxVeil. All rights reserved.</p>
        </div>
    </footer>



    <script src="js/validation.js?v=<?php echo time(); ?>"></script>
    <script src="js/progress.js?v=<?php echo time(); ?>"></script>
</body>
</html>
