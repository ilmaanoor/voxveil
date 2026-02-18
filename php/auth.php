<?php
require_once 'config.php';
require_once 'session.php';

header('Content-Type: application/json');

// Handle login and registration
$action = $_POST['action'] ?? '';

if ($action === 'register') {
    handleRegistration($conn);
} elseif ($action === 'login') {
    handleLogin($conn);
}

// Registration handler
function handleRegistration($conn) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Server-side validation
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }
    
    // Check if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    try {
        $stmt = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)");
        $stmt->execute([
            ':email' => $email,
            ':password_hash' => $passwordHash
        ]);
        
        $userId = $conn->lastInsertId();
        
        echo json_encode(['success' => true, 'message' => 'Registration successful! Please login with your credentials.']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
    }
}

// Login handler  
function handleLogin($conn) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = $_POST['remember'] ?? false;
    
    // Validation
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }
    
    // Get user from database
    $stmt = $conn->prepare("SELECT id, email, password_hash FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Verify password
    if (password_verify($password, $user['password_hash'])) {
        setUserSession($user['id'], $user['email']);
        
        // Set cookie if remember me is checked
        if ($remember) {
            setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 days
        }
        
        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
}
?>
