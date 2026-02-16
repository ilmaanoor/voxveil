-- VoxVeil Database Schema
-- Drop existing database if exists and create new one
DROP DATABASE IF EXISTS voxveil_db;
CREATE DATABASE voxveil_db;
USE voxveil_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User profiles table
CREATE TABLE user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    education ENUM('UG', 'PG', 'Employee', 'Speaker') NOT NULL,
    field VARCHAR(100) NOT NULL,
    purpose ENUM('Graduation Interview', 'Company Switch', 'Masters Interview', 'Skill Enhancement') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Interview sessions table
CREATE TABLE interview_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    duration INT DEFAULT 0,
    transcript TEXT,
    questions_answered INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Session metrics table
CREATE TABLE session_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    filler_count INT DEFAULT 0,
    words_per_minute INT DEFAULT 0,
    confidence_score INT DEFAULT 0,
    feedback TEXT,
    improvement_tips TEXT,
    FOREIGN KEY (session_id) REFERENCES interview_sessions(id) ON DELETE CASCADE
);

-- Insert sample data for testing
INSERT INTO users (email, password_hash) VALUES 
('demo@voxveil.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO user_profiles (user_id, name, education, field, purpose) VALUES
(1, 'Demo User', 'UG', 'BCA', 'Graduation Interview');
