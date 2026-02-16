/* Practice Page Specific Styles */
// practice.js - Voice Recognition and Practice Session Handler
// Implements: Web Speech API, Real-time Transcription, Filler Word Detection

let recognition;
let isRecording = false;
let transcript = '';
let fillerWords = ['um', 'uh', 'like', 'you know', 'actually', 'basically', 'literally', 'so'];
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
    $('#next-question-btn').on('click', loadNextQuestion);
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
            startTime = Date.now();

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

    if (transcript.trim()) {
        saveAnswer('voice');
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

// Update metrics display
function updateMetrics() {
    const duration = startTime ? (Date.now() - startTime) / 1000 : 0;
    const wpm = duration > 0 ? Math.round((wordCount / duration) * 60) : 0;

    $('#filler-count').text(fillerCount);
    $('#word-count').text(wordCount);
    $('#wpm-display').text(wpm);

    // Update confidence score (simple calculation)
    const fillerRatio = wordCount > 0 ? fillerCount / wordCount : 0;
    const confidenceScore = Math.max(0, Math.min(100, 100 - (fillerRatio * 200)));
    $('#confidence-score').text(Math.round(confidenceScore) + '%');
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
    showAlert(`Answer saved! Input type: ${inputType}`, 'success');

    // Reset for next question
    setTimeout(() => {
        transcript = '';
        $('#transcript-display').html('<p class="text-muted">Your answer will appear here...</p>');
    }, 1000);
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
    const questions = $('#question-display').data('questions');
    if (!questions || questions.length === 0) return;

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

    const avgWpm = duration > 0 ? Math.round((totalWords / duration) * 60) : 0;
    const fillerRatio = totalWords > 0 ? totalFillers / totalWords : 0;
    const confidenceScore = Math.max(0, Math.min(100, 100 - (fillerRatio * 200)));

    const sessionData = {
        action: 'save_session',
        duration: duration,
        transcript: fullTranscript,
        questions_answered: sessionQuestions.length,
        filler_count: totalFillers,
        wpm: avgWpm,
        confidence_score: Math.round(confidenceScore),
        feedback: generateFeedback(totalFillers, avgWpm, confidenceScore)
    };

    $('#end-session-btn').prop('disabled', true).html('Saving...');

    $.post('php/practice-handler.php', sessionData, function (response) {
        if (response.success) {
            showAlert('Session saved successfully! Redirecting to progress...', 'success');
            setTimeout(() => {
                window.location.href = 'progress.php';
            }, 1500);
        } else {
            showAlert('Failed to save session', 'error');
            $('#end-session-btn').prop('disabled', false).html('End Session');
        }
    }, 'json');
}

// Generate feedback
function generateFeedback(fillers, wpm, confidence) {
    let feedback = [];

    if (fillers > 10) {
        feedback.push('Try to reduce filler words. Take short pauses instead.');
    } else if (fillers < 5) {
        feedback.push('Great job minimizing filler words!');
    }

    if (wpm < 100) {
        feedback.push('Your pace is slow. Try to speak a bit faster.');
    } else if (wpm > 160) {
        feedback.push('You\'re speaking too fast. Slow down for clarity.');
    } else {
        feedback.push('Excellent speaking pace!');
    }

    if (confidence < 60) {
        feedback.push('Practice more to build confidence.');
    } else if (confidence >= 80) {
        feedback.push('Outstanding confidence level!');
    }

    return feedback.join(' ');
}
