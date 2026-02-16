<?php
require_once 'config.php';
require_once 'session.php';

header('Content-Type: application/json');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = $_SESSION['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }
    
    if ($action === 'save_profile') {
        $name = $_POST['name'] ?? '';
        $education = $_POST['education'] ?? '';
        $field = $_POST['field'] ?? '';
        $purpose = $_POST['purpose'] ?? '';
        
        // Validation
        if (empty($name) || empty($education) || empty($field) || empty($purpose)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }
        
        require_once 'db-operations.php';
        $db = new DatabaseOperations($conn);
        
        // Check if profile exists
        $existingProfile = $db->getUserProfile($userId);
        
        if ($existingProfile) {
            // Update existing profile
            $result = $db->updateUserProfile($userId, $name, $education, $field, $purpose);
        } else {
            // Insert new profile
            $result = $db->insertUserProfile($userId, $name, $education, $field, $purpose);
        }
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Profile saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save profile']);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get user profile
    $userId = $_SESSION['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }
    
    require_once 'db-operations.php';
    $db = new DatabaseOperations($conn);
    $profile = $db->getUserProfile($userId);
    
    if ($profile) {
        echo json_encode(['success' => true, 'profile' => $profile]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No profile found']);
    }
}
?>
