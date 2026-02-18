/* Practice Page Specific Styles */
// practice.js - Voice Recognition and Practice Session Handler
// Implements: Web Speech API, Real-time Transcription, Filler Word Detection

let recognition;
let isRecording = false;
let transcript = '';
let fillerWords = [
    'um', 'uh', 'like', 'you know', 'actually', 'basically', 'literally', 'so',
    'I mean', 'right', 'okay', 'sort of', 'kind of', 'well'
];
let fillerCount = 0;
let wordCount = 0;
let startTime;
let currentQuestion = '';
let sessionQuestions = [];

$(document).ready(function () {
    // Check for browser support
    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
        showAlert('Your browser does not support speech recognition. Please use Chrome or Edge.', 'warning');
        $('#mic-btn').prop('disabled', true);
    } else {
        initializeSpeechRecognition();
    }

    // Load questions from server
    loadQuestions();

    // Event Handlers
    $('#mic-btn').on('click', toggleRecording);
    $('#text-input').on('keypress', handleTextInput);
    $('#submit-answer-btn').on('click', submitTextAnswer);
    $('#submit-voice-btn').on('click', function () {
        if (!transcript.trim()) {
            showAlert('Please record some audio first!', 'warning');
            return;
        }
        saveAnswer('voice');
        $(this).prop('disabled', true);
        alert('Transcript submitted successfully!'); // Using a visible block to confirm
    });
    $('#next-question-btn').on('click', loadNextQuestion);
    $('#redo-btn').on('click', handleRedo);
    $('#end-session-btn').on('click', endSession);
});

// Initialize Speech Recognition
function initializeSpeechRecognition() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();

    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = 'en-US';
    recognition.maxAlternatives = 1;

    recognition.onstart = function () {
        console.log('Speech recognition started');
        isRecording = true;
        updateRecordingUI(true);
    };

    recognition.onresult = function (event) {
        let interimTranscript = '';
        let finalTranscript = '';

        for (let i = event.resultIndex; i < event.results.length; i++) {
            const transcriptPiece = event.results[i][0].transcript;

            if (event.results[i].isFinal) {
                finalTranscript += transcriptPiece + ' ';
                analyzeForFillers(transcriptPiece);
            } else {
                interimTranscript += transcriptPiece;
            }
        }

        transcript += finalTranscript;
        displayTranscript(transcript + interimTranscript);
        updateMetrics();
    };

    recognition.onerror = function (event) {
        console.error('Speech recognition error:', event.error);
        showAlert('Error: ' + event.error, 'error');
        isRecording = false;
        updateRecordingUI(false);
    };

    recognition.onend = function () {
        if (isRecording) {
            recognition.start(); // Restart if still recording
        } else {
            updateRecordingUI(false);
        }
    };
}

// Toggle recording
function toggleRecording() {
    if (!isRecording) {
        startRecording();
    } else {
        stopRecording();
    }
}

// Start recording
function startRecording() {
    // Request microphone permission first
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(function (stream) {
            // Permission granted, start recognition
            transcript = '';
            fillerCount = 0;
            wordCount = 0;
            const sessionStartTime = Date.now();
            const startDate = new Date(sessionStartTime);
            $('#session-start-time').text(startDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));

            recognition.start();
            $('#transcript-display').html('<p class="text-muted">🎤 Listening... Start speaking!</p>');
            updateMetrics();

            // Stop the stream (we only needed permission check)
            stream.getTracks().forEach(track => track.stop());
        })
        .catch(function (err) {
            showAlert('Microphone access denied. Please allow microphone in browser settings.', 'error');
            console.error('Microphone error:', err);
        });
}

// Stop recording
function stopRecording() {
    isRecording = false;
    recognition.stop();
    console.log("Recording stopped. Transcript length:", transcript.trim().length);

    if (transcript.trim()) {
        showAlert('Recording stopped. You can now click "Submit Voice Answer" to save.', 'info');
    }
}

// Update UI based on recording state
function updateRecordingUI(recording) {
    const micBtn = $('#mic-btn');
    const micIcon = $('#mic-icon');

    if (recording) {
        micBtn.removeClass('btn-primary').addClass('btn-danger');
        micBtn.html('🛑 Stop Recording');
        micIcon.addClass('recording-pulse');
    } else {
        micBtn.removeClass('btn-danger').addClass('btn-primary');
        micBtn.html('🎤 Start Speaking');
        micIcon.removeClass('recording-pulse');
    }
}

// Display transcript
function displayTranscript(text) {
    const highlightedText = highlightFillers(text);
    $('#transcript-display').html(`<p>${highlightedText}</p>`);
}

// Highlight filler words in transcript
function highlightFillers(text) {
    let highlighted = text;
    fillerWords.forEach(filler => {
        const regex = new RegExp(`\\b${filler}\\b`, 'gi');
        highlighted = highlighted.replace(regex, `<span class="filler-highlight">${filler}</span>`);
    });
    return highlighted;
}

// Analyze text for filler words
function analyzeForFillers(text) {
    const words = text.toLowerCase().split(/\s+/);
    wordCount += words.length;

    words.forEach(word => {
        if (fillerWords.includes(word)) {
            fillerCount++;
        }
    });
}

// Update metrics display (Strict Weighted Logic)
function updateMetrics() {
    // Use unified strict scoring
    const metrics = calculateStrictScore(wordCount, fillerCount, startTime, currentQuestion, transcript);

    $('#filler-count').text(metrics.fillerCount);
    $('#word-count').text(metrics.wordCount);
    $('#wpm-display').text(metrics.wpm);
    $('#confidence-score').text(Math.round(metrics.score) + '%');
    return metrics;
}

// Unified Strict Scoring Logic
function calculateStrictScore(words, fillers, start, question, currentTranscript) {
    const duration = start ? (Date.now() - start) / 1000 : 0;
    const wpm = duration > 1 ? Math.round((words / duration) * 60) : 0;

    // 1. Filler Penalty (30%)
    const fillerRatio = words > 0 ? fillers / words : 0;
    const fillerScore = Math.max(0, 100 - (fillerRatio * 2000));

    // 2. Pace Score (20%)
    let paceScore = 0;
    if (wpm >= 120 && wpm <= 160) paceScore = 100;
    else if (wpm > 0) paceScore = Math.max(0, 100 - Math.abs(140 - wpm) * 2);

    // 3. Linguistic Depth (20%)
    // CRITICAL: Harsh penalty for very short answers
    let lengthScore = 0;
    if (words > 30) lengthScore = 100;
    else if (words > 0) lengthScore = (words / 35) * 100;

    // 4. Relevance Score (30%)
    const relevanceScore = calculateRelevance(question, currentTranscript);

    // Weighted Average
    let finalScore = (fillerScore * 0.3) + (paceScore * 0.2) + (lengthScore * 0.2) + (relevanceScore * 0.3);

    // NONSENSE PROTECTION: If it's just "hi" or "hello", tank the score hard
    if (words < 5 || relevanceScore < 15) {
        finalScore = finalScore * 0.2; // 80% penalty for nonsense
    }

    return {
        score: Math.min(100, Math.max(0, finalScore)),
        wpm: wpm,
        fillerRatio: fillerRatio,
        relevance: relevanceScore,
        wordCount: words,
        fillerCount: fillers
    };
}

// Calculate Relevance between Question and Answer
function calculateRelevance(question, answer) {
    if (!answer || !question) return 0;
    const stopWords = ['a', 'an', 'the', 'is', 'are', 'was', 'were', 'in', 'on', 'at', 'to', 'for', 'with', 'about', 'tell', 'me', 'what', 'how', 'why', 'your', 'my', 'you', 'i'];

    function getKeywords(text) {
        return text.toLowerCase()
            .replace(/[^\w\s]/g, '')
            .split(/\s+/)
            .filter(word => word.length > 2 && !stopWords.includes(word));
    }

    const qKeywords = getKeywords(question);
    if (qKeywords.length === 0) return 100;

    let matches = 0;
    const cleanAnswer = answer.toLowerCase();
    qKeywords.forEach(kw => {
        if (cleanAnswer.includes(kw)) matches++;
    });

    const matchDensity = (matches / qKeywords.length) * 100;
    const interviewMarkers = ['experience', 'skill', 'goal', 'project', 'because', 'learned', 'challeng', 'result'];
    let markersFound = 0;
    interviewMarkers.forEach(m => {
        if (cleanAnswer.includes(m)) markersFound++;
    });

    return Math.min(100, matchDensity + (markersFound * 15));
}

// Handle text input (keypress event)
function handleTextInput(e) {
    if (e.which === 13 && !e.shiftKey) { // Enter key without shift
        e.preventDefault();
        submitTextAnswer();
    }
}

// Submit text answer
function submitTextAnswer() {
    const textAnswer = $('#text-input').val().trim();

    if (!textAnswer) {
        showAlert('Please type your answer', 'warning');
        return;
    }

    transcript = textAnswer;
    analyzeForFillers(textAnswer);
    displayTranscript(textAnswer);
    updateMetrics();

    saveAnswer('text');
    $('#text-input').val('');
}

// Save answer
function saveAnswer(inputType) {
    const answer = {
        question: currentQuestion,
        answer: transcript,
        inputType: inputType,
        fillerCount: fillerCount,
        wordCount: wordCount,
        timestamp: new Date().toISOString()
    };

    sessionQuestions.push(answer);
    showAlert(`Answer saved! Total answers: ${sessionQuestions.length}`, 'success');
    $('#answers-saved').text(sessionQuestions.length);

    // Show redo button
    $('#redo-btn').fadeIn();

    // Reset for next question (wait a bit longer for review)
    setTimeout(() => {
        if (!isRecording) {
            // Only clear UI if not currently recording another attempt
            // transcript = ''; // Keep transcript visible so they can review/redo
        }
    }, 1000);
}

// Handle Redo Answer
function handleRedo() {
    if (sessionQuestions.length === 0) return;

    if (!confirm('Are you sure you want to delete this attempt and try again?')) {
        return;
    }

    // Remove the last saved answer
    sessionQuestions.pop();

    // Reset data
    transcript = '';
    fillerCount = 0;
    wordCount = 0;

    // Reset UI
    $('#transcript-display').html('<p class="text-muted">Your answer will appear here...</p>');
    $('#text-input').val('');
    updateMetrics();

    // Hide redo button until next save
    $('#redo-btn').fadeOut();

    showAlert('Previous attempt deleted. You can talk or type again now.', 'warning');
}

// Load questions from server
function loadQuestions() {
    $.get('php/practice-handler.php?action=get_questions', function (response) {
        if (response.success) {
            const allQuestions = [...response.questions.general, ...response.questions.technical];
            $('#question-display').data('questions', allQuestions);

            // Show profile info
            if (response.profile) {
                const profileMsg = `Questions customized for: ${response.profile.field} - ${response.profile.purpose}`;
                console.log(profileMsg);
            }

            loadNextQuestion();
        } else {
            showAlert('Failed to load questions', 'error');
        }
    }, 'json').fail(function () {
        showAlert('Error loading questions from server', 'error');
        // Load default questions as fallback
        const defaultQuestions = [
            'Tell me about yourself.',
            'What are your strengths and weaknesses?',
            'Why should we hire you?',
            'Where do you see yourself in 5 years?',
            'Describe a challenging situation and how you handled it.'
        ];
        $('#question-display').data('questions', defaultQuestions);
        loadNextQuestion();
    });
}

// Load next question
function loadNextQuestion() {
    // 1. Reset all state for a clean slate
    transcript = '';
    fillerCount = 0;
    wordCount = 0;
    startTime = null; // Important: reset timer for new WPM calculation

    // 2. Hide UI buttons on new question
    $('#redo-btn').hide();

    // 3. Clear transcript display
    $('#transcript-display').html('<p class="text-muted">Your answer will appear here...</p>');

    // 4. Reset metrics dashboard
    updateMetrics();

    const questionsData = $('#question-display').data('questions');
    if (!questionsData || questionsData.length === 0) return;

    // Update Question Counter
    const answeredCount = sessionQuestions.length;
    $('#question-counter').text(`${answeredCount + 1} / ${questionsData.length}`);
    $('#answers-saved').text(answeredCount);

    // 5. Re-enable submit button (Crucial fix)
    $('#submit-voice-btn').prop('disabled', false).html('Submit Voice Answer');

    const questions = questionsData;

    const randomIndex = Math.floor(Math.random() * questions.length);
    currentQuestion = questions[randomIndex];

    $('#question-display').html(`
        <div class="question-card fade-in">
            <h4>Interview Question</h4>
            <p>${currentQuestion}</p>
        </div>
    `);
}

// End practice session
function endSession() {
    if (sessionQuestions.length === 0) {
        showAlert('You need to answer at least one question', 'warning');
        return;
    }

    if (!confirm('Are you sure you want to end this practice session?')) {
        return;
    }

    const duration = startTime ? Math.round((Date.now() - startTime) / 1000) : 0;

    // Calculate overall metrics
    let totalFillers = 0;
    let totalWords = 0;
    let fullTranscript = '';

    sessionQuestions.forEach(q => {
        totalFillers += q.fillerCount;
        totalWords += q.wordCount;
        fullTranscript += `Q: ${q.question}\nA: ${q.answer}\n\n`;
    });

    // Use unified strict score for overall session
    const sessionMetrics = calculateStrictScore(totalWords, totalFillers, startTime, "Overall Interview", fullTranscript);

    // Calculate average relevance
    let totalRelevance = 0;
    sessionQuestions.forEach(q => {
        totalRelevance += calculateRelevance(q.question, q.answer);
    });
    const avgRelevance = sessionQuestions.length > 0 ? totalRelevance / sessionQuestions.length : 0;

    const sessionData = {
        action: 'save_session',
        duration: duration,
        transcript: fullTranscript,
        questions_answered: sessionQuestions.length,
        filler_count: totalFillers,
        wpm: sessionMetrics.wpm,
        confidence_score: Math.round(sessionMetrics.score),
        relevance_score: Math.round(avgRelevance), // Added relevance_score
        feedback: generateFeedback(totalFillers, sessionMetrics.wpm, sessionMetrics.score, avgRelevance) // Pass avgRelevance to feedback
    };

    $('#end-session-btn').prop('disabled', true).html('Saving...');

    $.post('php/practice-handler.php', sessionData, function (response) {
        console.log("Session save response:", response);
        if (response.success) {
            showAlert('Session saved! redirecting to results...', 'success');
            // Change body content to show clear ending message
            $('.practice-page-wrapper .container').html(`
                <div class="glass-card text-center py-5 fade-in">
                    <h1 class="text-gradient mb-3">Session Ended!</h1>
                    <p class="h4 mb-4">Great job on finishing your practice.</p>
                    <div class="alert alert-success mb-4">
                        Redirecting you to the progress page to see your detailed remarks...
                    </div>
                    <a href="progress.php" class="btn btn-primary">Go to Progress Now</a>
                </div>
            `);
            window.location.href = 'progress.php';
        } else {
            showAlert('Failed to save session', 'error');
            $('#end-session-btn').prop('disabled', false).html('End Session');
        }
    }, 'json');
}

// Enhanced feedback generator with strict remarks
function generateFeedback(totalFillers, avgWpm, confidence, avgRelevance) {
    let remarks = [];

    // 1. Filler Remarks
    const fillerRatio = totalFillers / (sessionQuestions.reduce((acc, q) => acc + q.wordCount, 0) || 1);
    if (fillerRatio > 0.05) {
        remarks.push('⚠️ High filler usage detected. You are using verbal tics more than 5% of the time, which impacts your authority. Try to embrace silence between thoughts.');
    } else if (fillerRatio > 0.02) {
        remarks.push('Noticeable fillers used. Good effort, but aim for a cleaner delivery next time.');
    } else {
        remarks.push('✅ Excellent linguistic clarity! You sound very polished.');
    }

    // 2. Pace Remarks
    if (avgWpm < 110 && avgWpm > 0) {
        remarks.push('🐢 Pace is too slow. Aim for 130-150 WPM to maintain interviewer engagement.');
    } else if (avgWpm > 170) {
        remarks.push('🚀 Speaking too fast. It makes you sound nervous. Try to slow down and emphasize key outcomes.');
    } else {
        remarks.push('🎯 Stable and professional speaking pace.');
    }

    // 3. Relevance Remarks (Strict Anti-Nonsense)
    if (avgRelevance < 40) {
        remarks.push('🔴 Low relevance score. Many of your answers were off-topic or lacked depth. Ensure you are addressing technical keywords directly.');
    } else if (avgRelevance < 70) {
        remarks.push('⚠️ Moderate relevance. Your answers are on-topic but could include more specific examples or technical terminology.');
    } else {
        remarks.push('🎯 Highly relevant answers! You addressed the core of the questions effectively.');
    }

    // 4. Final Verdict
    if (confidence > 80 && avgRelevance > 70) {
        remarks.push('✨ Outstanding performance! You are highly prepared for this interview.');
    } else if (confidence < 50 || avgRelevance < 40) {
        remarks.push('⛔ Needs Attention. We recommend focusing on structural clarity and reducing distractions in your delivery.');
    }

    return remarks.join(' ');
}
