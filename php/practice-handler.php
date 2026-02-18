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
    $education = $profile['education'] ?? 'UG';
    
    // 1. Field-specific (Technical/Industry)
    $fieldBank = [
        'BCA' => [
            'How do you stay updated with the latest trends in software development?',
            'Describe your experience with version control systems like Git.',
            'What is the most challenging bug you have ever fixed?',
            'Explain the concept of RESTful APIs to a non-technical person.',
            'What are your thoughts on the impact of AI on software engineering?'
        ],
        'PHYSICS' => [
            'How would you explain the significance of the Higgs Boson?',
            'Describe a complex physics problem you solved using mathematical modeling.',
            'What is your understanding of quantum entanglement?',
            'How do you approach laboratory data analysis and error calculation?',
            'What role does physics play in sustainable energy solutions?'
        ],
        'COMMERCE' => [
            'How do you analyze a company\'s financial health using its balance sheet?',
            'Explain the impact of global inflation on local retail markets.',
            'What is your strategy for effective risk management in a volatile economy?',
            'How do you handle ethical dilemmas in financial reporting?',
            'What are the key drivers of consumer behavior in the digital age?'
        ],
        'FOOD PROCESSING' => [
            'Explain the role of HACCP in ensuring food safety.',
            'How does dehydration affect the nutritional value of processed foods?',
            'What are the latest innovations in sustainable food packaging?',
            'Describe the process of pasteurization and its critical control points.',
            'How do you manage supply chain integrity in food production?'
        ],
        'JOB' => [
            'Tell me about a time you had to learn a new technology quickly.',
            'How do you handle tight deadlines and multiple project priorities?',
            'Describe a situation where you had to work with a difficult colleague.',
            'What is your approach to giving and receiving constructive feedback?',
            'How do you align your personal goals with the company\'s mission?'
        ],
        'SILQ' => [
            'How does EcoRoute AI handle real-time traffic data unpredictability?',
            'Explain the mathematical foundation of your CO2 optimization algorithm.',
            'SILQ emphasizes "Engineering Excellence." How is your code tested for reliability?',
            'How would you integrate block-chain for transparent supply chain tracking in EcoRoute?',
            'What is the next major feature you would add to EcoRoute AI?',
            'How do you handle data privacy when gathering user route preferences?'
        ]
    ];
    
    // 2. Purpose-specific (Goal-oriented)
    $purposeBank = [
        'graduation' => [
            'What was the most important lesson you learned during your final year project?',
            'How has your education prepared you for the professional world?',
            'What is your plan for the first six months after you graduate?',
            'Describe a time you showed leadership in a student organization.',
            'How do you plan to continue your learning after graduation?'
        ],
        'company_switch' => [
            'What specific values are you looking for in your next employer?',
            'How will your previous experience translate to our specific industry?',
            'Tell me about a time you improved a process at your current job.',
            'What is the biggest risk you have taken in your career so far?',
            'How do you handle the transition period when joining a new team?'
        ],
        'masters' => [
            'What specific research gap do you aim to address in your Master\'s studies?',
            'How does this specific university align with your academic aspirations?',
            'Describe your experience with academic writing and peer review.',
            'How do you plan to contribute to the university\'s research community?',
            'What is your long-term vision for your academic career?'
        ],
        'skill_enhancement' => [
            'Which specific skill do you believe is currently the most critical in your field?',
            'How do you measure the ROI of the time you spend on self-improvement?',
            'Describe a time you taught yourself a complex skill without formal training.',
            'How do you filter through the noise to find quality learning resources?',
            'What is your current "learning roadmap" for the next 12 months?'
        ]
    ];

    // 3. Status-specific (Experience-based)
    $statusBank = [
        'UG' => [
            'Tell me about a group project where you had to resolve a conflict.',
            'What subjects did you find most challenging and why?',
            'How do you balance your academics with extracurricular activities?',
            'What motivated you to choose your current major?'
        ],
        'PG' => [
            'How does your postgraduate specialization differentiate you in the market?',
            'Describe your thesis/dissertation topic in simple terms.',
            'How have your career goals evolved since your undergraduate studies?',
            'What advanced research methodologies are you most proficient in?'
        ],
        'Employee' => [
            'How do you maintain a work-life balance in a demanding role?',
            'Describe a time you had to mentor a junior team member.',
            'What is your strategy for staying relevant in an evolving industry?',
            'How do you handle high-pressure decision-making situations?'
        ],
        'Speaker' => [
            'How do you tailor your communication style for different audiences?',
            'Describe a time a presentation didn\'t go as planned and how you recovered.',
            'What is your process for researching and preparing a new keynote?',
            'How do you handle difficult questions from a live audience?'
        ]
    ];
    
    // 4. General behavioral (Core)
    $generalBank = [
        'Tell me about yourself and your journey so far.',
        'What are your three greatest strengths and one major weakness?',
        'Describe a significant failure and what you learned from it.',
        'Where do you see yourself in five years?',
        'Why should we choose you over other qualified candidates?',
        'How do you define success in your professional life?'
    ];
    
    // Logic to ensure "Entirely Different" sets
    $questions = [];
    
    // Add 4 field questions
    $fQs = $fieldBank[$field] ?? $generalBank;
    shuffle($fQs);
    $questions = array_merge($questions, array_slice($fQs, 0, 4));
    
    // Add 4 purpose questions
    $pQs = $purposeBank[$purpose] ?? $generalBank;
    shuffle($pQs);
    $questions = array_merge($questions, array_slice($pQs, 0, 4));
    
    // Add 3 status questions
    $sQs = $statusBank[$education] ?? $generalBank;
    shuffle($sQs);
    $questions = array_merge($questions, array_slice($sQs, 0, 3));
    
    // Add 3 general questions
    shuffle($generalBank);
    $questions = array_merge($questions, array_slice($generalBank, 0, 3));
    
    // Final unique set
    $allQuestions = array_values(array_unique($questions));
    
    echo json_encode([
        'success' => true,
        'questions' => [
            'general' => $allQuestions,
            'technical' => $fQs
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
        'recent_sessions' => array_slice($sessions, 0, 10)
    ]);
}
?>
