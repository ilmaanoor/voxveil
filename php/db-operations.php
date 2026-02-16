<?php
require_once 'config.php';
require_once 'session.php';

// Database operations class for SQLite
class DatabaseOperations {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // CREATE - Insert user profile
    public function insertUserProfile($userId, $name, $education, $field, $purpose) {
        $stmt = $this->conn->prepare("INSERT INTO user_profiles (user_id, name, education, field, purpose) VALUES (:user_id, :name, :education, :field, :purpose)");
        return $stmt->execute([
            ':user_id' => $userId,
            ':name' => $name,
            ':education' => $education,
            ':field' => $field,
            ':purpose' => $purpose
        ]);
    }
    
    // CREATE - Insert interview session
    public function insertSession($userId, $duration, $transcript, $questionsAnswered) {
        $stmt = $this->conn->prepare("INSERT INTO interview_sessions (user_id, duration, transcript, questions_answered) VALUES (:user_id, :duration, :transcript, :questions_answered)");
        
        if ($stmt->execute([
            ':user_id' => $userId,
            ':duration' => $duration,
            ':transcript' => $transcript,
            ':questions_answered' => $questionsAnswered
        ])) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // CREATE - Insert session metrics
    public function insertMetrics($sessionId, $fillerCount, $wpm, $confidenceScore, $feedback) {
        $stmt = $this->conn->prepare("INSERT INTO session_metrics (session_id, filler_count, words_per_minute, confidence_score, feedback) VALUES (:session_id, :filler_count, :wpm, :confidence_score, :feedback)");
        
        return $stmt->execute([
            ':session_id' => $sessionId,
            ':filler_count' => $fillerCount,
            ':wpm' => $wpm,
            ':confidence_score' => $confidenceScore,
            ':feedback' => $feedback
        ]);
    }
    
    // READ - Get user profile
    public function getUserProfile($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // READ - Get all sessions for a user
    public function getUserSessions($userId) {
        $stmt = $this->conn->prepare("SELECT s.*, m.* FROM interview_sessions s LEFT JOIN session_metrics m ON s.id = m.session_id WHERE s.user_id = :user_id ORDER BY s.session_date DESC");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // READ - Get single session
    public function getSession($sessionId) {
        $stmt = $this->conn->prepare("SELECT s.*, m.* FROM interview_sessions s LEFT JOIN session_metrics m ON s.id = m.session_id WHERE s.id = :session_id");
        $stmt->execute([':session_id' => $sessionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // UPDATE - Update user profile
    public function updateUserProfile($userId, $name, $education, $field, $purpose) {
        $stmt = $this->conn->prepare("UPDATE user_profiles SET name = :name, education = :education, field = :field, purpose = :purpose WHERE user_id = :user_id");
        
        return $stmt->execute([
            ':user_id' => $userId,
            ':name' => $name,
            ':education' => $education,
            ':field' => $field,
            ':purpose' => $purpose
        ]);
    }
    
    // UPDATE - Update session
    public function updateSession($sessionId, $duration, $transcript) {
        $stmt = $this->conn->prepare("UPDATE interview_sessions SET duration = :duration, transcript = :transcript WHERE id = :session_id");
        
        return $stmt->execute([
            ':session_id' => $sessionId,
            ':duration' => $duration,
            ':transcript' => $transcript
        ]);
    }
    
    // DELETE - Delete session
    public function deleteSession($sessionId) {
        $stmt = $this->conn->prepare("DELETE FROM interview_sessions WHERE id = :session_id");
        return $stmt->execute([':session_id' => $sessionId]);
    }
    
    // Get progress statistics
    public function getProgressStats($userId) {
        $stmt = $this->conn->prepare("SELECT 
            COUNT(*) as total_sessions,
            AVG(m.confidence_score) as avg_confidence,
            AVG(m.words_per_minute) as avg_wpm,
            AVG(s.duration) as avg_duration,
            SUM(s.duration) as total_practice_time
        FROM interview_sessions s
        LEFT JOIN session_metrics m ON s.id = m.session_id
        WHERE s.user_id = :user_id");
        
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
