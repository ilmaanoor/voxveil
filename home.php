<?php
require_once 'php/session.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoxVeil - Home</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="container">
            <div class="logo">VoxVeil</div>
            <ul class="nav-links">
                <li><a href="home.php" class="active">Home</a></li>
                <li><a href="form.php">Get Started</a></li>
                <li><a href="practice.php">Practice</a></li>
                <li><a href="progress.php">Progress</a></li>
                <li><a href="history.php">History</a></li>
                <li><a href="#" id="logout-btn">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-grid">
                <article class="hero-content fade-in">
                    <h1 class="text-gradient">Welcome to VoxVeil</h1>
                    <p class="hero-subtitle">Your Personal Interview Practice Platform</p>
                    <p class="hero-description">
                        Master the art of interviewing with our AI-powered platform. Practice your responses, 
                        receive instant feedback, and track your improvement over time. Whether you're preparing 
                        for your first job or making a career transition, VoxVeil is here to help you succeed.
                    </p>
                    <a href="form.php" class="btn btn-primary">Start Practicing Now</a>
                </article>

                <aside class="hero-image fade-in">
                    <div class="image-wrapper clay-card">
                        <!-- Animated clay-style mic scene -->
                        <div class="clay-scene">
                            <div class="clay-mic">
                                <div class="mic-head">🎤</div>
                                <div class="mic-stand"></div>
                                <div class="mic-base"></div>
                            </div>
                            <div class="clay-wave w1"></div>
                            <div class="clay-wave w2"></div>
                            <div class="clay-wave w3"></div>
                            <div class="clay-orb o1"></div>
                            <div class="clay-orb o2"></div>
                            <div class="clay-orb o3"></div>
                            <div class="clay-label">Speaking...</div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <h2 class="text-center text-gradient">About VoxVeil</h2>
            <p class="text-center about-description">
                VoxVeil is designed to help you overcome common interview challenges like filler words, 
                speaking pace, and confidence. Our platform provides a safe, private environment to practice 
                and improve.
            </p>
            <div class="features-grid">
                <article class="feature-card clay-card fade-in">
                    <div class="feature-icon-large">🎤</div>
                    <h3>Voice Recognition</h3>
                    <p>Practice speaking naturally using our advanced speech recognition technology. Get real-time transcription and analysis of your responses.</p>
                </article>
                <article class="feature-card clay-card fade-in">
                    <div class="feature-icon-large">📊</div>
                    <h3>Progress Tracking</h3>
                    <p>Monitor your improvement with detailed analytics. Track metrics like confidence scores, speaking pace, and filler word usage across sessions.</p>
                </article>
                <article class="feature-card clay-card fade-in">
                    <div class="feature-icon-large">💡</div>
                    <h3>Personalized Feedback</h3>
                    <p>Receive tailored suggestions based on your performance. Learn what you're doing well and where you can improve.</p>
                </article>
                <article class="feature-card clay-card fade-in">
                    <div class="feature-icon-large">🔒</div>
                    <h3>Private &amp; Secure</h3>
                    <p>Your practice sessions are completely private. All data is securely stored and only accessible to you.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-card clay-card text-center">
                <h2>Ready to Transform Your Interview Skills?</h2>
                <p>Click below to fill out a quick form and start your practice journey</p>
                <a href="form.php" class="btn btn-primary">Fill the Form</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>VoxVeil</h4>
                    <p>Master your interview skills with confidence</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="home.php">Home</a></li>
                        <li><a href="practice.php">Practice</a></li>
                        <li><a href="progress.php">Progress</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Connect With Us</h4>
                    <div class="social-links">
                        <a href="#" class="social-icon">📘</a>
                        <a href="#" class="social-icon">🐦</a>
                        <a href="#" class="social-icon">💼</a>
                        <a href="#" class="social-icon">📷</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 VoxVeil. All rights reserved.</p>
            </div>
        </div>
    </footer>



    <script>
        $(document).ready(function() {
            // Logout functionality
            $('#logout-btn').on('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to logout?')) {
                    window.location.href = 'php/session.php?action=logout';
                }
            });

            // Add fade-in animation on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            document.querySelectorAll('.fade-in').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'all 0.6s ease';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
