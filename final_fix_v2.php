<?php
require_once 'php/config.php';
try {
    $conn->exec("ALTER TABLE user_profiles ADD COLUMN updated_at DATETIME");
    echo "SUCCESS\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "duplicate column name") !== false) {
        echo "ALREADY EXISTS\n";
    } else {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}
?>
