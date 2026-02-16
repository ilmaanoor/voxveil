// Events.js - Event Handling with jQuery
// Implements: blur, focus, click, dblclick, keypress, 'this' keyword usage

$(document).ready(function () {
    console.log('VoxVeil Events Initialized');

    // Form toggle functionality - CLICK event
    $('.toggle-btn').on('click', function () {
        const formType = $(this).data('form');

        // 'this' keyword - refers to the clicked button
        $(this).addClass('active').siblings().removeClass('active');

        if (formType === 'login') {
            $('#login-form').removeClass('hidden').addClass('fade-in');
            $('#register-form').addClass('hidden');
        } else {
            $('#register-form').removeClass('hidden').addClass('fade-in');
            $('#login-form').addClass('hidden');
        }

        hideAlert();
    });

    // FOCUS event - Highlight input on focus
    $('.form-control').on('focus', function () {
        // 'this' keyword - refers to the focused input
        $(this).parent().addClass('focused');
        highlightField(this, true);
        hideError(this);
    });

    // BLUR event - Validate input when user leaves the field
    $('.form-control').on('blur', function () {
        // 'this' keyword - refers to the input that lost focus
        $(this).parent().removeClass('focused');
        resetFieldStyle(this);

        const inputId = $(this).attr('id');
        const inputValue = $(this).val();

        // Validate based on input type
        if (inputId.includes('email')) {
            const result = validateEmail(inputValue);
            if (!result.valid) {
                showError(this, result.message);
            }
        } else if (inputId.includes('password') && !inputId.includes('confirm')) {
            const result = validatePassword(inputValue);
            if (!result.valid) {
                showError(this, result.message);
            }
        } else if (inputId === 'confirm-password') {
            const password = $('#register-password').val();
            const result = validatePasswordMatch(password, inputValue);
            if (!result.valid) {
                showError(this, result.message);
            }
        }
    });

    // KEYPRESS event - Real-time validation feedback
    $('.form-control').on('keypress', function (e) {
        // 'this' keyword - refers to the input being typed in
        const input = $(this);

        // Remove error styling while typing
        input.removeClass('error');

        // Show character count for password fields
        if (input.attr('type') === 'password') {
            const currentLength = input.val().length + 1; // +1 for the key being pressed

            // Access CSS from JavaScript
            if (currentLength >= 6) {
                input.css('border-color', '#10b981');
            } else {
                input.css('border-color', '#f59e0b');
            }
        }
    });

    // DBLCLICK event on logo - Easter egg / fun interaction
    $('.logo').on('dblclick', function () {
        // 'this' keyword - refers to the logo element
        $(this).css({
            'transform': 'scale(1.2) rotate(360deg)',
            'transition': 'all 0.5s ease'
        });

        setTimeout(() => {
            $(this).css({
                'transform': 'scale(1) rotate(0deg)'
            });
        }, 500);
    });

    // jQuery selector - Select all buttons and add hover effect
    $('.btn').hover(
        function () {
            // Mouse enter - 'this' refers to hovered button
            $(this).css('transform', 'translateY(-2px)');
        },
        function () {
            // Mouse leave - 'this' refers to the button
            $(this).css('transform', 'translateY(0)');
        }
    );

    // Login form submission - CLICK via form submit
    $('#login-form').on('submit', function (e) {
        e.preventDefault();

        const formData = {
            email: $('#login-email').val(),
            password: $('#login-password').val(),
            remember: $('#remember-me').is(':checked'),
            action: 'login'
        };

        // Validate
        const emailResult = validateEmail(formData.email);
        const passwordResult = validatePassword(formData.password);

        if (!emailResult.valid) {
            showError(document.getElementById('login-email'), emailResult.message);
            return;
        }

        if (!passwordResult.valid) {
            showError(document.getElementById('login-password'), passwordResult.message);
            return;
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('Loading...');

        // AJAX POST request
        $.ajax({
            url: 'php/auth.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showAlert('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'home.php';
                    }, 1500);
                } else {
                    showAlert(response.message, 'error');
                    submitBtn.prop('disabled', false).html('Login to VoxVeil');
                }
            },
            error: function () {
                showAlert('An error occurred. Please try again.', 'error');
                submitBtn.prop('disabled', false).html('Login to VoxVeil');
            }
        });
    });

    // Registration form submission
    $('#register-form').on('submit', function (e) {
        e.preventDefault();

        const formData = {
            email: $('#register-email').val(),
            password: $('#register-password').val(),
            confirm_password: $('#confirm-password').val(),
            action: 'register'
        };

        // Validate
        const emailResult = validateEmail(formData.email);
        const passwordResult = validatePassword(formData.password);
        const matchResult = validatePasswordMatch(formData.password, formData.confirm_password);

        let hasError = false;

        if (!emailResult.valid) {
            showError(document.getElementById('register-email'), emailResult.message);
            hasError = true;
        }

        if (!passwordResult.valid) {
            showError(document.getElementById('register-password'), passwordResult.message);
            hasError = true;
        }

        if (!matchResult.valid) {
            showError(document.getElementById('confirm-password'), matchResult.message);
            hasError = true;
        }

        if (hasError) return;

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('Creating Account...');

        // AJAX POST request
        $.ajax({
            url: 'php/auth.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showAlert('Account created! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'home.php';
                    }, 1500);
                } else {
                    showAlert(response.message, 'error');
                    submitBtn.prop('disabled', false).html('Create Account');
                }
            },
            error: function () {
                showAlert('An error occurred. Please try again.', 'error');
                submitBtn.prop('disabled', false).html('Create Account');
            }
        });
    });

    // Prevent form default submission on Enter key
    $('.form-control').on('keypress', function (e) {
        if (e.which === 13) { // Enter key
            $(this).closest('form').submit();
        }
    });

    // Auto-focus first input on page load
    setTimeout(() => {
        $('#login-email').focus();
    }, 300);
});
