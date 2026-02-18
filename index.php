<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoxVeil - Login & Registration</title>
    <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="auth-wrapper">
        <div class="container">
            <div class="auth-container fade-in">
                <!-- Left Side - Branding -->
                <aside class="auth-branding">
                    <h1 class="text-gradient">VoxVeil</h1>
                    <p>Master your interview skills with AI-powered practice sessions</p>
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon">🎤</div>
                            <div>
                                <h4>Voice Practice</h4>
                                <p>Practice with speech recognition</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">📊</div>
                            <div>
                                <h4>Track Progress</h4>
                                <p>Monitor your improvement over time</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">💡</div>
                            <div>
                                <h4>Get Feedback</h4>
                                <p>Receive personalized tips</p>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Right Side - Forms -->
                <section class="auth-forms glass-card">
                    <div class="form-toggle">
                        <button class="toggle-btn active" data-form="login">Login</button>
                        <button class="toggle-btn" data-form="register">Register</button>
                    </div>

                    <div id="alert-container"></div>

                    <!-- Login Form -->
                    <form id="login-form" class="auth-form">
                        <h2>Welcome Back</h2>
                        <p class="form-subtitle">Continue your journey to interview excellence</p>

                        <div class="form-group">
                            <label for="login-email" class="form-label">Email Address</label>
                            <input 
                                type="email" 
                                id="login-email" 
                                name="email" 
                                class="form-control" 
                                placeholder="your.email@example.com"
                                required>
                            <span class="error-message" id="login-email-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="login-password" class="form-label">Password</label>
                            <input 
                                type="password" 
                                id="login-password" 
                                name="password" 
                                class="form-control" 
                                placeholder="Enter your password"
                                required>
                            <span class="error-message" id="login-password-error"></span>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" id="remember-me" name="remember">
                            <label for="remember-me">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            Login to VoxVeil
                        </button>
                    </form>

                    <!-- Registration Form -->
                    <form id="register-form" class="auth-form hidden">
                        <h2>Create Account</h2>
                        <p class="form-subtitle">Start your interview practice journey today</p>

                        <div class="form-group">
                            <label for="register-email" class="form-label">Email Address</label>
                            <input 
                                type="email" 
                                id="register-email" 
                                name="email" 
                                class="form-control" 
                                placeholder="your.email@example.com"
                                required>
                            <span class="error-message" id="register-email-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="register-password" class="form-label">Password</label>
                            <input 
                                type="password" 
                                id="register-password" 
                                name="password" 
                                class="form-control" 
                                placeholder="Create a password (min. 6 characters)"
                                required>
                            <span class="error-message" id="register-password-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="confirm-password" class="form-label">Confirm Password</label>
                            <input 
                                type="password" 
                                id="confirm-password" 
                                name="confirm_password" 
                                class="form-control" 
                                placeholder="Confirm your password"
                                required>
                            <span class="error-message" id="confirm-password-error"></span>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            Create Account
                        </button>
                    </form>
                </section>
            </div>
        </div>
    </div>

    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-xl) var(--spacing-md);
            background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.15), transparent 40%),
                        radial-gradient(circle at bottom left, rgba(236, 72, 153, 0.15), transparent 40%);
        }

        .auth-container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
        }

        .auth-branding h1 {
            font-size: clamp(3rem, 10vw, 5rem);
            margin-bottom: var(--spacing-md);
            letter-spacing: -2px;
            line-height: 1;
        }

        .auth-branding p {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: var(--spacing-xl);
            max-width: 450px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            animation: slideInLeft 0.5s ease-out forwards;
            opacity: 0;
        }

        .feature-item:nth-child(1) { animation-delay: 0.1s; }
        .feature-item:nth-child(2) { animation-delay: 0.2s; }
        .feature-item:nth-child(3) { animation-delay: 0.3s; }

        @keyframes slideInLeft {
            from { transform: translateX(-30px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .feature-icon {
            font-size: 2rem;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .feature-item h4 {
            margin-bottom: 0.25rem;
            font-size: 1.25rem;
        }

        .auth-forms {
            padding: 3rem;
        }

        .form-toggle {
            display: flex;
            background: rgba(255, 255, 255, 0.03);
            border-radius: var(--radius-full);
            padding: 0.5rem;
            margin-bottom: 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .toggle-btn {
            flex: 1;
            padding: 1rem;
            background: transparent;
            border: none;
            border-radius: var(--radius-full);
            color: var(--text-muted);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-base);
        }

        .toggle-btn.active {
            background: var(--gradient-1);
            color: white;
            box-shadow: var(--shadow-glow);
        }

        @media (max-width: 968px) {
            .auth-wrapper {
                padding: var(--spacing-xl) 0;
            }
            .auth-container {
                grid-template-columns: 1fr !important;
                gap: 3rem;
                padding: 0 var(--spacing-md);
            }
            .auth-branding {
                text-align: center;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .auth-branding p {
                margin-left: auto;
                margin-right: auto;
            }
            .feature-list {
                display: none;
            }
            .auth-forms {
                padding: 2rem;
            }
        }
    </style>

    <script src="js/validation.js?v=<?php echo time(); ?>"></script>
    <script src="js/events.js?v=<?php echo time(); ?>"></script>
</body>
</html>
