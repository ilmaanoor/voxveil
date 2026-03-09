<?php
require_once 'config.php';
require_once 'session.php';
require_once 'db-operations.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$db = new DatabaseOperations($conn);

// Handle different actions
if ($action === 'get_questions') {
    require_once 'questions-data.php';
    getPersonalizedQuestions($db, $userId);
} elseif ($action === 'save_session') {
    saveSession($db, $userId);
} elseif ($action === 'get_stats') {
    getStats($db, $userId);
}

// Get personalized questions based on user profile
function getPersonalizedQuestions($db, $userId) {
    global $questionBank;

    $profile = $db->getUserProfile($userId);
    
    $field = $profile['field'] ?? 'OTHER';
    $purpose = $profile['purpose'] ?? 'Skill Enhancement';
    $education = $profile['education'] ?? 'UG';
    
    // Normalize field key if needed (e.g. from form.php)
    // The keys in questions-data.php match form.php field values
    
    // 1. Field-specific (Technical/Industry)
    $fieldQs = $questionBank['fields'][$field] ?? $questionBank['fields']['OTHER'];
    
    // 2. Purpose-specific (Goal-oriented)
    $purposeQs = $questionBank['purposes'][$purpose] ?? $questionBank['purposes']['Skill Enhancement'];

    // 3. Status-specific (Experience-based)
    $statusQs = $questionBank['education'][$education] ?? $questionBank['education']['UG'];
    
    // 4. General behavioral (Core)
    $generalQs = $questionBank['general'];
    
    // Logic to select 14 questions (4 field, 4 purpose, 3 status, 3 general)
    $questions = [];
    
    // Add 4 field questions
    $fCopy = $fieldQs;
    shuffle($fCopy);
    $questions = array_merge($questions, array_slice($fCopy, 0, 4));
    
    // Add 4 purpose questions
    $pCopy = $purposeQs;
    shuffle($pCopy);
    $selectedPurposeQs = array_slice($pCopy, 0, 4);
    $questions = array_merge($questions, $selectedPurposeQs);
    
    // Add 3 status questions
    $sCopy = $statusQs;
    shuffle($sCopy);
    $selectedStatusQs = array_slice($sCopy, 0, 3);
    $questions = array_merge($questions, $selectedStatusQs);
    
    // Add 3 general questions
    $gCopy = $generalQs;
    shuffle($gCopy);
    $selectedGeneralQs = array_slice($gCopy, 0, 3);
    $questions = array_merge($questions, $selectedGeneralQs);
    
    // Final unique set
    $allQuestions = array_values(array_unique($questions));
    
    echo json_encode([
        'success' => true,
        'questions' => [
            'general' => $allQuestions,
            'technical' => $fCopy // Returning the shuffled technical pool
        ],
        'profile' => $profile
    ]);
}

// Save practice session
function saveSession($db, $userId) {
    $duration = $_POST['duration'] ?? 0;
    $transcript = $_POST['transcript'] ?? '';
    $questionsAnswered = $_POST['questions_answered'] ?? 0;
    $fillerCount = $_POST['filler_count'] ?? 0;
    $wpm = $_POST['wpm'] ?? 0;
    $confidenceScore = $_POST['confidence_score'] ?? 0;
    $relevanceScore = $_POST['relevance_score'] ?? 0;
    $feedback = $_POST['feedback'] ?? '';
    
    // Insert session
    $sessionId = $db->insertSession($userId, $duration, $transcript, $questionsAnswered);
    
    if ($sessionId) {
        // Insert metrics
        $metricsInserted = $db->insertMetrics($sessionId, $fillerCount, $wpm, $confidenceScore, $feedback, $relevanceScore);
        
        if ($metricsInserted) {
            echo json_encode(['success' => true, 'message' => 'Session saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save metrics']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save session']);
    }
}

// Get progress statistics
function getStats($db, $userId) {
    $stats = $db->getProgressStats($userId);
    $sessions = $db->getUserSessions($userId);
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recent_sessions' => $sessions
    ]);
}
?>
