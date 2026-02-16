// Validation.js - Form Validation using JavaScript and DOM Constraints
// Implements: DOM manipulation, Objects, Arrays, Form Validation

// Validation Rules Object
const validationRules = {
    email: {
        required: true,
        pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        message: 'Please enter a valid email address'
    },
    password: {
        required: true,
        minLength: 6,
        message: 'Password must be at least 6 characters long'
    },
    name: {
        required: true,
        minLength: 2,
        message: 'Name must be at least 2 characters long'
    }
};

// Validation Functions
function validateEmail(email) {
    if (!email || email.trim() === '') {
        return { valid: false, message: 'Email is required' };
    }
    
    if (!validationRules.email.pattern.test(email)) {
        return { valid: false, message: validationRules.email.message };
    }
    
    return { valid: true, message: '' };
}

function validatePassword(password) {
    if (!password || password.trim() === '') {
        return { valid: false, message: 'Password is required' };
    }
    
    if (password.length < validationRules.password.minLength) {
        return { valid: false, message: validationRules.password.message };
    }
    
    return { valid: true, message: '' };
}

function validateName(name) {
    if (!name || name.trim() === '') {
        return { valid: false, message: 'Name is required' };
    }
    
    if (name.length < validationRules.name.minLength) {
        return { valid: false, message: validationRules.name.message };
    }
    
    return { valid: true, message: '' };
}

function validatePasswordMatch(password, confirmPassword) {
    if (password !== confirmPassword) {
        return { valid: false, message: 'Passwords do not match' };
    }
    return { valid: true, message: '' };
}

// Show error message with DOM manipulation
function showError(inputElement, message) {
    const errorElement = document.getElementById(inputElement.id + '-error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
        inputElement.classList.add('error');
    }
}

// Hide error message
function hideError(inputElement) {
    const errorElement = document.getElementById(inputElement.id + '-error');
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.classList.remove('show');
        inputElement.classList.remove('error');
    }
}

// Show alert message using DOM
function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alert-container');
    if (!alertContainer) return;
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    // Accessing CSS from JavaScript - Change styles dynamically
    alertDiv.style.marginBottom = '1rem';
    
    alertContainer.innerHTML = '';
    alertContainer.appendChild(alertDiv);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transform = 'translateY(-20px)';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// Hide alert
function hideAlert() {
    const alertContainer = document.getElementById('alert-container');
    if (alertContainer) {
        alertContainer.innerHTML = '';
    }
}

// Validate entire form - returns array of errors
function validateForm(formData) {
    const errors = [];
    
    for (let key in formData) {
        if (key.includes('email')) {
            const result = validateEmail(formData[key]);
            if (!result.valid) {
                errors.push({ field: key, message: result.message });
            }
        } else if (key.includes('password') && !key.includes('confirm')) {
            const result = validatePassword(formData[key]);
            if (!result.valid) {
                errors.push({ field: key, message: result.message });
            }
        } else if (key.includes('name')) {
            const result = validateName(formData[key]);
            if (!result.valid) {
                errors.push({ field: key, message: result.message });
            }
        }
    }
    
    return errors;
}

// DOM Constraint - Add input constraints dynamically
function addInputConstraints(formElement) {
    const emailInputs = formElement.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.setAttribute('pattern', validationRules.email.pattern.source);
        input.setAttribute('required', 'required');
    });
    
    const passwordInputs = formElement.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.setAttribute('minlength', validationRules.password.minLength);
        input.setAttribute('required', 'required');
    });
}

// Access and modify CSS from JavaScript
function highlightField(element, isValid) {
    if (isValid) {
        element.style.borderColor = '#10b981'; // Success green
        element.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';
    } else {
        element.style.borderColor = '#ef4444'; // Error red
        element.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
    }
}

// Reset field styling
function resetFieldStyle(element) {
    element.style.borderColor = '';
    element.style.boxShadow = '';
}
