<?php
// Database Configuration - Using SQLite (no password needed!)
define('DB_FILE', __DIR__ . '/../database/voxveil.db');

// Create SQLite connection
try {
    $conn = new PDO('sqlite:' . DB_FILE);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if they don't exist
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS user_profiles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            education TEXT NOT NULL,
            field TEXT NOT NULL,
            purpose TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS interview_sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            session_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            duration INTEGER DEFAULT 0,
            transcript TEXT,
            questions_answered INTEGER DEFAULT 0,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS session_metrics (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            session_id INTEGER NOT NULL,
            filler_count INTEGER DEFAULT 0,
            words_per_minute INTEGER DEFAULT 0,
            confidence_score INTEGER DEFAULT 0,
            relevance_score INTEGER DEFAULT 0,
            feedback TEXT,
            improvement_tips TEXT,
            FOREIGN KEY (session_id) REFERENCES interview_sessions(id) ON DELETE CASCADE
        );
    ");

    // Migration: Add columns if they don't exist
    try {
        $conn->exec("ALTER TABLE session_metrics ADD COLUMN relevance_score INTEGER DEFAULT 0");
    } catch (Exception $e) { /* Column already exists */ }
    
    try {
        $conn->exec("ALTER TABLE user_profiles ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP");
    } catch (Exception $e) { /* Column already exists */ }
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Return connection for use in other files
return $conn;
?>
