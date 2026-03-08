<?php
$start = microtime(true);

// Simulate the logic in form-handler.php
require_once 'php/config.php';
require_once 'php/db-operations.php';

$userId = 1; // Assuming user ID 1 exists
$name = "Test User";
$education = "UG";
$field = "BCA";
$purpose = "Graduation Interview";

$db = new DatabaseOperations($conn);

echo "Start: $start\n";

$t1 = microtime(true);
$existingProfile = $db->getUserProfile($userId);
$t2 = microtime(true);
echo "Get Profile: " . ($t2 - $t1) . "s\n";

if ($existingProfile) {
    $result = $db->updateUserProfile($userId, $name, $education, $field, $purpose);
} else {
    $result = $db->insertUserProfile($userId, $name, $education, $field, $purpose);
}
$t3 = microtime(true);
echo "Save Profile: " . ($t3 - $t2) . "s\n";

$end = microtime(true);
echo "Total Time: " . ($end - $start) . "s\n";
?>
