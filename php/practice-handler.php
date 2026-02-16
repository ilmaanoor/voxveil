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
    getPersonalizedQuestions($db, $userId);
} elseif ($action === 'save_session') {
    saveSession($db, $userId);
} elseif ($action === 'get_stats') {
    getStats($db, $userId);
}

// Get personalized questions based on user profile
function getPersonalizedQuestions($db, $userId) {
    $profile = $db->getUserProfile($userId);
    
    $field = $profile['field'] ?? 'general';
    $purpose = $profile['purpose'] ?? 'general';
    
    // Question bank organized by field and purpose
    $questionBank = [
        'BCA' => [
            'What programming languages are you most comfortable with?',
            'Explain the difference between object-oriented and functional programming.',
            'What is your favorite project you\'ve worked on?',
            'How do you approach debugging complex code issues?',
            'What is a data structure and why is it important?'
        ],
        'PHYSICS' => [
            'Explain Newton\'s laws of motion in simple terms.',
            'What interested you in studying physics?',
            'Describe a physics experiment you found fascinating.',
            'How do you apply physics principles in everyday life?',
            'What is your favorite branch of physics and why?'
        ],
        'COMMERCE' => [
            'What do you understand by financial accounting?',
            'Explain the concept of supply and demand.',
            'What are your career goals in commerce?',
            'How do you stay updated with economic trends?',
            'What is your understanding of business ethics?'
        ],
        'FOOD PROCESSING' => [
            'What interests you about food processing technology?',
            'Explain the importance of food safety standards.',
            'What are common food preservation methods?',
            'How has technology changed the food industry?',
            'What is your experience with quality control?'
        ],
        'JOB' => [
            'Tell me about your current role and responsibilities.',
            'What are your biggest professional achievements?',
            'How do you handle workplace conflicts?',
            'What motivates you in your career?',
            'Where do you see yourself in 5 years?'
        ]
    ];
    
    // Purpose-specific questions
    $purposeQuestions = [
        'graduation' => [
            'Why did you choose this field of study?',
            'What are your strengths and weaknesses?',
            'Tell me about a challenging project during your studies.',
            'How do you handle stress and pressure?',
            'What makes you a good candidate for this role?'
        ],
        'company_switch' => [
            'Why are you looking to change companies?',
            'What can you bring to our organization?',
            'Describe a situation where you led a team.',
            'How do you handle criticism?',
            'What is your greatest professional achievement?'
        ],
        'masters' => [
            'Why do you want to pursue a master\'s degree?',
            'What are your research interests?',
            'How will this program help your career goals?',
            'Describe your academic achievements.',
            'What makes you stand out from other applicants?'
        ],
        'skill_enhancement' => [
            'What new skills are you looking to develop?',
            'How do you stay updated in your field?',
            'Describe a time you learned something challenging.',
            'What is your learning style?',
            'How do you measure your progress?'
        ]
    ];
    
    // General behavioral questions
    $generalQuestions = [
        'Tell me about yourself.',
        'What are your hobbies and interests?',
        'How do you work in a team?',
        'Describe a difficult situation and how you handled it.',
        'What is your biggest accomplishment?',
        'How do you prioritize tasks?',
        'What are your long-term career goals?',
        'Why should we hire you?'
    ];
    
    // Combine questions based on profile
    $fieldQuestions = $questionBank[$field] ?? $generalQuestions;
    $purposeQs = $purposeQuestions[$purpose] ?? [];
    
    $allQuestions = array_merge($fieldQuestions, $purposeQs, $generalQuestions);
    
    echo json_encode([
        'success' => true,
        'questions' => [
            'general' => $allQuestions,
            'technical' => $fieldQuestions
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
    $feedback = $_POST['feedback'] ?? '';
    
    // Insert session
    $sessionId = $db->insertSession($userId, $duration, $transcript, $questionsAnswered);
    
    if ($sessionId) {
        // Insert metrics
        $metricsInserted = $db->insertMetrics($sessionId, $fillerCount, $wpm, $confidenceScore, $feedback);
        
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
        'recent_sessions' => array_slice($sessions, 0, 10)
    ]);
}
?>
