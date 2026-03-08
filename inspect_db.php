<?php
require_once 'php/config.php';
require_once 'php/db-operations.php';

echo "Database File: " . DB_FILE . "\n";
if (file_exists(DB_FILE)) {
    echo "Database exists.\n";
} else {
    echo "Database NOT found!\n";
}

try {
    $stmt = $conn->query("PRAGMA table_info(user_profiles)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "user_profiles columns:\n";
    foreach ($columns as $column) {
        echo "- " . $column['name'] . " (" . $column['type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
