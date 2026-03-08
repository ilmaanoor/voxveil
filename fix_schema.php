<?php
require_once 'php/config.php';
try {
    $conn->exec("ALTER TABLE user_profiles ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP");
    echo "Column updated_at added successfully to user_profiles.\n";
} catch (Exception $e) {
    echo "Error or column already exists: " . $e->getMessage() . "\n";
}

try {
    $conn->exec("ALTER TABLE session_metrics ADD COLUMN relevance_score INTEGER DEFAULT 0");
    echo "Column relevance_score added successfully to session_metrics.\n";
} catch (Exception $e) {
    echo "Error or column already exists: " . $e->getMessage() . "\n";
}
?>
