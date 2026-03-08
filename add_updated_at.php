<?php
require_once 'php/config.php';
$stmt = $conn->prepare("ALTER TABLE user_profiles ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP");
if ($stmt->execute()) {
    echo "SUCCESS: updated_at added.\n";
} else {
    print_r($stmt->errorInfo());
}
?>
