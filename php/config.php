<?php
// Database Configuration - Using SQLite (no password needed!)
define('DB_FILE', __DIR__ . '/../database/voxveil.db');

// Create SQLite connection
try {
    $conn = new PDO('sqlite:' . DB_FILE);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SQLite-specific optimization: increase busy timeout
    $conn->exec("PRAGMA busy_timeout = 5000;");
    $conn->exec("PRAGMA journal_mode = WAL;");
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Return connection for use in other files
return $conn;
?>
