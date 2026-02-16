<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoxVeil - Login & Registration</title>
    <link rel="stylesheet" href="css/styles.css">
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
            padding: var(--spacing-lg) 0;
        }

        .auth-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-xl);
            align-items: center;
        }

        .auth-branding h1 {
            font-size: 4rem;
            margin-bottom: var(--spacing-md);
        }

        .auth-branding > p {
            font-size: 1.25rem;
            margin-bottom: var(--spacing-xl);
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .feature-item {
            display: flex;
            gap: var(--spacing-md);
            align-items: flex-start;
        }

        .feature-icon {
            font-size: 2.5rem;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient-1);
            border-radius: var(--radius-md);
            flex-shrink: 0;
        }

        .feature-item h4 {
            margin-bottom: 0.25rem;
        }

        .feature-item p {
            font-size: 0.95rem;
            margin: 0;
        }

        .auth-forms {
            padding: var(--spacing-xl);
        }

        .form-toggle {
            display: flex;
            background: var(--bg-secondary);
            border-radius: var(--radius-full);
            padding: 0.375rem;
            margin-bottom: var(--spacing-lg);
        }

        .toggle-btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: none;
            border-radius: var(--radius-full);
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all var(--transition-base);
        }

        .toggle-btn.active {
            background: var(--gradient-1);
            color: white;
        }

        .auth-form h2 {
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: var(--text-muted);
            margin-bottom: var(--spacing-lg);
        }

        .w-full {
            width: 100%;
        }

        @media (max-width: 968px) {
            .auth-container {
                grid-template-columns: 1fr;
            }

            .auth-branding {
                text-align: center;
            }

            .feature-list {
                max-width: 500px;
                margin: 0 auto;
            }
        }

        @media (max-width: 480px) {
            .auth-branding h1 {
                font-size: 3rem;
            }

            .feature-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }
    </style>

    <script src="js/validation.js"></script>
    <script src="js/events.js"></script>
</body>
</html>
