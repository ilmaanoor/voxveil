<?php
require_once 'php/session.php';
requireLogin();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoxVeil - User Profile Form</title>
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
                <li><a href="form.php" class="active">Get Started</a></li>
                <li><a href="practice.php">Practice</a></li>
                <li><a href="progress.php">Progress</a></li>
                <li><a href="history.php">History</a></li>
            </ul>
        </nav>
    </header>

    <div class="form-page-wrapper">
        <div class="container">
            <div class="form-page-container fade-in">
                <h1 class="text-center text-gradient">Tell Us About Yourself</h1>
                <p id="profile-last-updated" class="text-center text-muted mb-4" style="font-size: 0.8rem; display: none;">
                    Profile Last Updated: <span id="update-date">Never</span>
                </p>
                <p class="text-center form-page-subtitle">
                    Help us personalize your interview practice experience
                </p>

                <!-- Progress Indicator -->
                <div class="progress-indicator">
                    <div class="progress-step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-label">Basic Info</div>
                    </div>
                    <div class="progress-step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-label">Education</div>
                    </div>
                    <div class="progress-step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-label">Goals</div>
                    </div>
                </div>

                <form id="profile-form" class="glass-card">
                    <div id="alert-container"></div>

                    <!-- Step 1: Basic Information -->
                    <div class="form-step active" data-step="1">
                        <h3>Basic Information</h3>
                        
                        <div class="form-group">
                            <label for="user-name" class="form-label">Full Name <span style="color: var(--error)">*</span></label>
                            <input 
                                type="text" 
                                id="user-name" 
                                name="name" 
                                class="form-control" 
                                placeholder="Enter your full name"
                                required>
                            <span class="error-message" id="user-name-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="education-level" class="form-label">Current Status <span style="color: var(--error)">*</span></label>
                            <select id="education-level" name="education" class="form-control" required>
                                <option value="">Select your status</option>
                                <option value="UG">Undergraduate Student (UG)</option>
                                <option value="PG">Postgraduate Student (PG)</option>
                                <option value="Employee">Working Professional</option>
                                <option value="Speaker">Public Speaker/Trainer</option>
                            </select>
                            <span class="error-message" id="education-level-error"></span>
                        </div>

                        <button type="button" class="btn btn-primary next-btn" data-next="2">
                            Continue to Education
                        </button>
                    </div>

                    <!-- Step 2: Education/Work Details -->
                    <div class="form-step" data-step="2">
                        <h3>Education & Work Details</h3>
                        
                        <div class="form-group">
                            <label for="field-of-study" class="form-label">Field of Study / Work Area <span style="color: var(--error)">*</span></label>
                            <select id="field-of-study" name="field" class="form-control" required>
                                <option value="">Select your field</option>
                                <!-- Academic Fields -->
                                <optgroup label="Academic Fields">
                                    <option value="BCA">BCA (Computer Applications)</option>
                                    <option value="PHYSICS">Physics</option>
                                    <option value="FOOD PROCESSING">Food Processing</option>
                                    <option value="COMMERCE">Commerce</option>
                                    <option value="ENGINEERING">Engineering</option>
                                    <option value="MEDICINE">Medicine</option>
                                    <option value="ARTS">Arts & Humanities</option>
                                </optgroup>
                                <!-- Work Fields -->
                                <optgroup label="Professional Fields">
                                    <option value="IT">Information Technology</option>
                                    <option value="FINANCE">Finance & Banking</option>
                                    <option value="MARKETING">Marketing & Sales</option>
                                    <option value="HR">Human Resources</option>
                                    <option value="HEALTHCARE">Healthcare</option>
                                    <option value="EDUCATION">Education & Training</option>
                                    <option value="OTHER">Other</option>
                                </optgroup>
                            </select>
                            <span class="error-message" id="field-of-study-error"></span>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary prev-btn" data-prev="1">
                                Back
                            </button>
                            <button type="button" class="btn btn-primary next-btn" data-next="3">
                                Continue to Goals
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Interview Purpose -->
                    <div class="form-step" data-step="3">
                        <h3>Interview Goals</h3>
                        
                        <div class="form-group">
                            <label class="form-label">What is the purpose of your interview practice? <span style="color: var(--error)">*</span></label>
                            
                            <div class="radio-group">
                                <label class="radio-card">
                                    <input type="radio" name="purpose" value="Graduation Interview">
                                    <div class="radio-content">
                                        <div class="radio-icon">🎓</div>
                                        <h4>Graduation Interviews</h4>
                                        <p>Preparing for campus placements and first job</p>
                                    </div>
                                </label>

                                <label class="radio-card">
                                    <input type="radio" name="purpose" value="Company Switch">
                                    <div class="radio-content">
                                        <div class="radio-icon">🔄</div>
                                        <h4>Company Switch</h4>
                                        <p>Moving from one company to another</p>
                                    </div>
                                </label>

                                <label class="radio-card">
                                    <input type="radio" name="purpose" value="Masters Interview">
                                    <div class="radio-content">
                                        <div class="radio-icon">📚</div>
                                        <h4>Masters Interview</h4>
                                        <p>Applying for postgraduate programs</p>
                                    </div>
                                </label>

                                <label class="radio-card">
                                    <input type="radio" name="purpose" value="Skill Enhancement">
                                    <div class="radio-content">
                                        <div class="radio-icon">💪</div>
                                        <h4>Skill Enhancement</h4>
                                        <p>Improving thinking and speaking skills</p>
                                    </div>
                                </label>
                            </div>
                            <span class="error-message" id="purpose-error"></span>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary prev-btn" data-prev="2">
                                Back
                            </button>
                            <button type="submit" class="btn btn-success">
                                Save & Start Practicing
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p class="text-center">&copy; 2026 VoxVeil. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .form-page-wrapper {
            min-height: calc(100vh - 200px);
            padding: 3rem 0;
        }

        .form-page-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .progress-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--spacing-xl);
            position: relative;
        }

        .progress-indicator::before {
            content: '';
            position: absolute;
            top: 30px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: var(--bg-tertiary);
            z-index: 0;
        }

        .progress-step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--spacing-xs);
            position: relative;
            z-index: 1;
        }

        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--bg-tertiary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            transition: all var(--transition-base);
        }

        .progress-step.active .step-number {
            background: var(--gradient-1);
            box-shadow: var(--shadow-glow);
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.4s ease-out;
        }

        .btn-group {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-lg);
        }

        .radio-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-md);
        }

        .radio-content {
            padding: var(--spacing-md);
            background: var(--bg-secondary);
            border: 2px solid var(--glass-border);
            border-radius: var(--radius-md);
            transition: all var(--transition-base);
            text-align: center;
        }

        @media (max-width: 600px) {
            .progress-indicator::before {
                display: none;
            }
            .radio-group {
                grid-template-columns: 1fr;
            }
            .progress-step .step-label {
                display: none;
            }
            .progress-step.active .step-label {
                display: block;
                font-size: 0.75rem;
            }
        }
    </style>

    <script src="js/validation.js?v=<?php echo time(); ?>"></script>
    <script>
        $(document).ready(function() {
            // Check for redirection alerts
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('alert') === 'complete_profile') {
                alert('Please complete your profile in this "Get Started" section before starting a practice session.');
            }

            // Existing logic
            let currentStep = 1;

            // Load existing profile data
            $.get('php/form-handler.php', function(response) {
                if (response.success && response.profile) {
                    $('#user-name').val(response.profile.name);
                    $('#education-level').val(response.profile.education);
                    $('#field-of-study').val(response.profile.field);
                    $(`input[name="purpose"][value="${response.profile.purpose}"]`).prop('checked', true);
                    
                    if (response.profile.updated_at) {
                        const date = new Date(response.profile.updated_at);
                        $('#update-date').text(date.toLocaleDateString() + ' ' + date.toLocaleTimeString());
                        $('#profile-last-updated').fadeIn();
                    }
                }
            });

            // Next button click event
            $('.next-btn').on('click', function() {
                const nextStep = parseInt($(this).data('next'));
                
                // Validate current step
                if (validateStep(currentStep)) {
                    currentStep = nextStep;
                    showStep(currentStep);
                }
            });

            // Previous button click event
            $('.prev-btn').on('click', function() {
                const prevStep = parseInt($(this).data('prev'));
                currentStep = prevStep;
                showStep(currentStep);
            });

            // Show specific step
            function showStep(step) {
                $('.form-step').removeClass('active');
                $(`.form-step[data-step="${step}"]`).addClass('active');

                $('.progress-step').removeClass('active');
                $(`.progress-step[data-step="${step}"]`).addClass('active');

                // Mark previous steps as completed
                $('.progress-step').each(function() {
                    const stepNum = parseInt($(this).data('step'));
                    if (stepNum < step) {
                        $(this).addClass('completed');
                    } else {
                        $(this).removeClass('completed');
                    }
                });
            }

            // Validate each step
            function validateStep(step) {
                let isValid = true;

                if (step === 1) {
                    const name = $('#user-name').val();
                    const education = $('#education-level').val();

                    if (!name || name.trim() === '') {
                        showError(document.getElementById('user-name'), 'Name is required');
                        isValid = false;
                    }

                    if (!education) {
                        showError(document.getElementById('education-level'), 'Please select your status');
                        isValid = false;
                    }
                } else if (step === 2) {
                    const field = $('#field-of-study').val();

                    if (!field) {
                        showError(document.getElementById('field-of-study'), 'Please select your field');
                        isValid = false;
                    }
                }

                return isValid;
            }

            // Form submission
            $('#profile-form').on('submit', function(e) {
                e.preventDefault();

                // Validate purpose selection
                const purpose = $('input[name="purpose"]:checked').val();
                if (!purpose) {
                    showAlert('Please select your interview purpose', 'error');
                    return;
                }

                const formData = {
                    action: 'save_profile',
                    name: $('#user-name').val(),
                    education: $('#education-level').val(),
                    field: $('#field-of-study').val(),
                    purpose: purpose
                };

                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('Saving...');

                $.post('php/form-handler.php', formData, function(response) {
                    if (response.success) {
                        showAlert('Profile saved successfully! Redirecting to practice...', 'success');
                        setTimeout(() => {
                            window.location.href = 'practice.php';
                        }, 1500);
                    } else {
                        showAlert(response.message, 'error');
                        submitBtn.prop('disabled', false).html('Save & Start Practicing');
                    }
                }, 'json');
            });

            // Blur event for validation
            $('.form-control').on('blur', function() {
                const value = $(this).val();
                if (value && value.trim() !== '') {
                    hideError(this);
                }
            });

            // Focus event for field highlighting
            $('.form-control').on('focus', function() {
                hideError(this);
                highlightField(this, true);
            });

            // Remove highlight on blur
            $('.form-control').on('blur', function() {
                resetFieldStyle(this);
            });
        });
    </script>
</body>
</html>
