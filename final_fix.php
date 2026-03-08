<?php
require_once 'php/config.php';
try {
    $conn->exec("ALTER TABLE user_profiles ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP");
    echo "SUCCESS\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
