<?php
session_start();

// Session management functions

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if session has expired (30 minutes timeout)
function checkSessionTimeout() {
    $timeout = 1800; // 30 minutes in seconds
    
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > $timeout) {
            session_unset();
            session_destroy();
            return false;
        }
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

// Require login - redirect to index.php if not logged in
function requireLogin() {
    if (!isLoggedIn() || !checkSessionTimeout()) {
        header("Location: index.php");
        exit();
    }
}

// Get current user ID
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Set user session
function setUserSession($userId, $email) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['email'] = $email;
    $_SESSION['last_activity'] = time();
}

// Destroy user session
function logout() {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
