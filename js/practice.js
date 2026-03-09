/* Practice Page Specific Styles */
// practice.js - Voice Recognition and Practice Session Handler
// Implements: Web Speech API, Real-time Transcription, Filler Word Detection

let recognition;
let isRecording = false;
let transcript = '';
// Filler words split into two groups:
// Group 1 - Sound fillers: Chrome FILTERS these from final transcript, we must catch from interim
let soundFillers = ['um', 'uh', 'umh', 'umm', 'ummm', 'uhh', 'uhhh', 'ah', 'err', 'hmm'];
// Group 2 - Word fillers: Normal words Chrome KEEPS in transcript, we scan transcript for these
let wordFillersList = ['like', 'you know', 'actually', 'basically', 'literally', 'so', 'i mean', 'right', 'okay', 'sort of', 'kind of', 'well'];
// Combined list for highlighting
let fillerWords = [...soundFillers, ...wordFillersList];

let fillerCount = 0;
let wordCount = 0;
let interimFillerTotal = 0;    // Accumulated sound-fillers from interim results
let currentSegmentFillers = 0; // Max sound-fillers seen in current interim segment
let startTime; // Reset per question
let globalSessionStartTime; // Persists for the whole session
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
                // Finalized — lock in whatever sound-fillers we caught in interim for this segment
                interimFillerTotal += currentSegmentFillers;
                currentSegmentFillers = 0;

                finalTranscript += transcriptPiece + ' ';
                console.log('[VoxVeil] Final chunk:', transcriptPiece, '| interimFillerTotal so far:', interimFillerTotal);
            } else {
                interimTranscript += transcriptPiece;

                // Catch sound fillers (um/uh) from interim before Chrome deletes them
                const seenSoundFillers = extractSoundFillers(interimTranscript);
                if (seenSoundFillers > currentSegmentFillers) {
                    currentSegmentFillers = seenSoundFillers;
                    console.log('[VoxVeil] Caught interim sound fillers:', currentSegmentFillers, 'in:', interimTranscript);
                }
            }
        }

        transcript += finalTranscript;
        console.log('[VoxVeil] Full transcript now:', transcript);
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
            interimFillerTotal = 0;
            currentSegmentFillers = 0;
            const sessionStartTime = Date.now();

            // Set individual question start time if not set
            if (!startTime) startTime = sessionStartTime;

            // Set global start time if not set (first action of session)
            if (!globalSessionStartTime) globalSessionStartTime = sessionStartTime;

            const startDate = new Date(globalSessionStartTime);
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
    if (!text) return '';
    let highlighted = text;
    // Simple standard boundary for highlighting visual only
    const sortedFillers = [...fillerWords].sort((a, b) => b.length - a.length);
    const pattern = sortedFillers.map(f => f.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|');
    const regex = new RegExp(`\\b(${pattern})\\b`, 'gi');

    return highlighted.replace(regex, `<span class="filler-highlight">$&</span>`);
}

// Count sound-type fillers (um/uh/hmm) from text — returns a NUMBER
function extractSoundFillers(text) {
    if (!text) return 0;
    const normalized = ' ' + text.toLowerCase().replace(/[^a-z0-9']/gi, ' ').replace(/\s+/g, ' ') + ' ';
    let count = 0;
    let workText = normalized;
    soundFillers.forEach(filler => {
        const paddedFiller = ' ' + filler.toLowerCase() + ' ';
        while (workText.includes(paddedFiller)) {
            count++;
            workText = workText.replace(paddedFiller, ' ');
        }
    });
    return count;
}

// Count word-type fillers (like/so/actually) from text — these survive in the final transcript
function countWordFillers(text) {
    if (!text) return 0;
    const normalized = ' ' + text.toLowerCase().replace(/[^a-z0-9']/gi, ' ').replace(/\s+/g, ' ') + ' ';
    let count = 0;
    let workText = normalized;
    wordFillersList.forEach(filler => {
        const paddedFiller = ' ' + filler.toLowerCase() + ' ';
        while (workText.includes(paddedFiller)) {
            count++;
            workText = workText.replace(paddedFiller, ' ');
        }
    });
    console.log('[VoxVeil] Word fillers in transcript:', count);
    return count;
}

// Extract list of ALL filler words found in text (for legacy compatibility)
function extractFillers(text) {
    if (!text) return [];
    const normalized = ' ' + text.toLowerCase().replace(/[^a-z0-9']/gi, ' ').replace(/\s+/g, ' ') + ' ';
    let found = [];
    let workText = normalized;
    const sorted = [...fillerWords].sort((a, b) => b.length - a.length);
    sorted.forEach(filler => {
        const paddedFiller = ' ' + filler.toLowerCase() + ' ';
        while (workText.includes(paddedFiller)) {
            found.push(filler.toLowerCase());
            workText = workText.replace(paddedFiller, ' ');
        }
    });
    return found;
}

// Foolproof filler word counting logic avoiding regex boundary quirks
function getFillerCount(text) {
    if (!text) return 0;
    // Normalize string: all punctuation to spaces, reduce multiple spaces to one
    const normalized = ' ' + text.toLowerCase().replace(/[^a-z0-9']/gi, ' ').replace(/\s+/g, ' ') + ' ';
    let count = 0;

    // Create a dynamic pattern that looks for padded words
    const sorted = [...fillerWords].sort((a, b) => b.length - a.length);

    // We must use a loop and replace to avoid overlapping matches
    let workText = normalized;
    sorted.forEach(filler => {
        const paddedFiller = ' ' + filler.toLowerCase() + ' ';
        // Keep searching and replacing until no more matches of this specific filler
        while (workText.includes(paddedFiller)) {
            count++;
            // Replace with a space so we don't break subsequent word boundaries
            // e.g. " um like " -> " like "
            workText = workText.replace(paddedFiller, ' ');
        }
    });

    return count;
}

// Analyze text for filler words
function analyzeForFillers(text) {
    if (!text) return;
    const words = text.toLowerCase().split(/\s+/).filter(w => w.trim().length > 0);

    // We don't increment wordCount here anymore because it's calculated in updateMetrics
    // to avoid double counting from final/interim overlaps.

    words.forEach(word => {
        if (fillerWords.includes(word)) {
            fillerCount++;
        }
    });
}

// Update metrics display (Strict Weighted Logic)
function updateMetrics() {
    // Calculate word count from the actual transcript text
    const currentWords = transcript.trim() ? transcript.trim().split(/\s+/).length : 0;

    // COMBINED filler count:
    // 1. interimFillerTotal = sound fillers (um/uh) caught from interim speech before Chrome deletes them
    // 2. countWordFillers(transcript) = word fillers (like/so/basically) that survive in final transcript text
    // 3. currentSegmentFillers = sound fillers currently being spoken RIGHT NOW
    const soundFillersTotal = interimFillerTotal + currentSegmentFillers;
    const wordFillersTotal = countWordFillers(transcript);
    fillerCount = soundFillersTotal + wordFillersTotal;
    console.log('[VoxVeil] updateMetrics — soundFillers:', soundFillersTotal, '| wordFillers:', wordFillersTotal, '| total:', fillerCount);

    wordCount = currentWords;

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
    const now = Date.now();
    const duration = start ? (now - start) / 1000 : 0;

    // MINIMUM DURATION: Avoid 0 WPM for very short answers
    const wpm = (duration > 0.5) ? Math.round((words / duration) * 60) : 0;

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
    let finalScore = (fillerScore * 0.25) + (paceScore * 0.2) + (lengthScore * 0.25) + (relevanceScore * 0.3);

    // DEPTH BONUS: Reward longer, relevant answers
    if (words > 50 && relevanceScore > 60) finalScore += 5;

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
    const answerWords = answer.trim().split(/\s+/).length;
    // Require at least 10 words in the answer to get any meaningful relevance score
    if (answerWords < 5) return 5;

    const stopWords = ['a', 'an', 'the', 'is', 'are', 'was', 'were', 'in', 'on', 'at', 'to', 'for', 'with', 'about', 'tell', 'me', 'what', 'how', 'why', 'your', 'my', 'you', 'i', 'do', 'did', 'can', 'have', 'has', 'just', 'that', 'this', 'and', 'or', 'but', 'so', 'if'];

    function getKeywords(text) {
        return text.toLowerCase()
            .replace(/[^\w\s]/g, ' ')
            .split(/\s+/)
            .filter(word => word.length > 3 && !stopWords.includes(word));
    }

    const qKeywords = getKeywords(question);

    // If question has no meaningful keywords, return a neutral 40 — not 100!
    if (qKeywords.length === 0) return 40;

    let matches = 0;
    const cleanAnswer = answer.toLowerCase();
    qKeywords.forEach(kw => {
        if (cleanAnswer.includes(kw)) matches++;
    });

    // Base score on keyword match density
    const matchDensity = (matches / qKeywords.length) * 70; // Max 70 from keywords

    // Interview depth markers add a small bonus (capped)
    const interviewMarkers = ['experience', 'skill', 'goal', 'project', 'because', 'learned', 'challeng', 'result', 'achieved', 'solved'];
    let markersFound = 0;
    interviewMarkers.forEach(m => {
        if (cleanAnswer.includes(m)) markersFound++;
    });
    const markerBonus = Math.min(markersFound * 5, 20); // Max 20 bonus, reduced from 15/marker

    // Length bonus: longer answers get a base credit (up to 10)
    const lengthBonus = Math.min(Math.floor(answerWords / 10), 10);

    return Math.min(100, Math.round(matchDensity + markerBonus + lengthBonus));
}

// Handle text input (keypress event)
function handleTextInput(e) {
    // Start timers on first keypress
    const now = Date.now();
    if (!startTime) startTime = now;
    if (!globalSessionStartTime) globalSessionStartTime = now;

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
    interimFillerTotal = 0;
    currentSegmentFillers = 0;

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
    interimFillerTotal = 0;
    currentSegmentFillers = 0;
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
    $('#submit-voice-btn').prop('disabled', false).html('Submit Transcript');

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
    console.log("Ending session... Questions answered:", sessionQuestions.length);

    if (sessionQuestions.length === 0) {
        showAlert('You need to answer at least one question', 'warning');
        return;
    }

    if (!confirm('Are you sure you want to end this practice session?')) {
        return;
    }

    $('#end-session-btn').prop('disabled', true).html('Saving...');

    try {
        const now = Date.now();
        // Fallback: If globalSessionStartTime is not set (it should be), use now - 30s as a guess
        // but better to ensure it's set on first action.
        let duration = 0;
        if (globalSessionStartTime) {
            duration = Math.round((now - globalSessionStartTime) / 1000);
        } else if (sessionQuestions.length > 0) {
            // If we have questions but no start time, use a conservative estimate
            duration = sessionQuestions.length * 45; // 45 seconds per question avg
        }

        console.log("Session Duration Calculated:", duration, "secs");

        let totalFillers = 0;
        let totalWords = 0;
        let fullTranscript = '';
        let totalRelevance = 0;

        sessionQuestions.forEach((q, idx) => {
            totalFillers += parseInt(q.fillerCount) || 0;
            totalWords += parseInt(q.wordCount) || 0;
            fullTranscript += `Q${idx + 1}: ${q.question}\nA: ${q.answer}\n\n`;
            totalRelevance += calculateRelevance(q.question, q.answer);
        });

        const avgRelevance = totalRelevance / sessionQuestions.length;

        // Final overall metrics calculation
        const sessionMetrics = calculateStrictScore(totalWords, totalFillers, globalSessionStartTime, "Overall Interview", fullTranscript);
        console.log("Final Metrics Calculated:", sessionMetrics);

        if (!sessionMetrics) {
            throw new Error("Failed to calculate session metrics");
        }

        const sessionData = {
            action: 'save_session',
            duration: duration,
            transcript: fullTranscript,
            questions_answered: sessionQuestions.length,
            filler_count: totalFillers,
            wpm: sessionMetrics.wpm || 0,
            confidence_score: Math.round(sessionMetrics.score) || 0,
            relevance_score: Math.round(avgRelevance) || 0,
            feedback: generateFeedback(totalFillers, sessionMetrics.wpm, sessionMetrics.score, avgRelevance)
        };

        console.log("Sending sessionData to server:", sessionData);

        $.post('php/practice-handler.php', sessionData, function (response) {
            console.log("Server Response:", response);
            if (response.success) {
                showAlert('Session saved! redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = 'progress.php';
                }, 1000);
            } else {
                showAlert('Failed to save session: ' + (response.message || 'Unknown error'), 'error');
                $('#end-session-btn').prop('disabled', false).html('End Session');
            }
        }, 'json').fail(function (xhr, status, error) {
            console.error("AJAX POST failed:", status, error);
            showAlert('Network error: Could not save session', 'error');
            $('#end-session-btn').prop('disabled', false).html('End Session');
        });

    } catch (err) {
        console.error("Fatal error in endSession:", err);
        showAlert('App Error: ' + err.message, 'error');
        $('#end-session-btn').prop('disabled', false).html('End Session');
    }
}

// Dynamic, data-driven feedback generator — unique every session
function generateFeedback(totalFillers, avgWpm, confidence, avgRelevance) {
    const totalWords = sessionQuestions.reduce((acc, q) => acc + (parseInt(q.wordCount) || 0), 0);
    const fillerRatio = totalWords > 0 ? (totalFillers / totalWords) : 0;
    const fillerPct = Math.round(fillerRatio * 100);
    const questionsCount = sessionQuestions.length;
    const roundedWpm = Math.round(avgWpm);
    const roundedConf = Math.round(confidence);
    const roundedRel = Math.round(avgRelevance);

    let parts = [];

    // Helper function to pick a random feedback string from an array
    const pickRandom = (arr) => arr[Math.floor(Math.random() * arr.length)];

    // 1. Opening based on overall score
    if (confidence >= 80 && avgRelevance >= 70) {
        parts.push(pickRandom([
            `🌟 Outstanding session! You hit ${roundedConf}% confidence across ${questionsCount} question(s).`,
            `✨ Brilliant work today. You tackled ${questionsCount} question(s) with an impressive ${roundedConf}% confidence.`,
            `💪 Fantastic preparation showing through. ${questionsCount} handled with ${roundedConf}% confidence.`,
            `🏆 Top-tier performance! A very strong ${roundedConf}% confidence score on ${questionsCount} question(s).`
        ]));
    } else if (confidence >= 60) {
        parts.push(pickRandom([
            `📈 Decent run! ${roundedConf}% confidence over ${questionsCount} question(s). Definitely room to grow.`,
            `👍 A solid effort on those ${questionsCount} question(s). You achieved ${roundedConf}% confidence.`,
            `🎯 You got through ${questionsCount} question(s) with ${roundedConf}% confidence — keep up the practice!`,
            `💡 Good baseline session. ${roundedConf}% confidence over ${questionsCount} question(s). Let's aim higher next time.`
        ]));
    } else {
        parts.push(pickRandom([
            `📉 You completed ${questionsCount} question(s) with ${roundedConf}% confidence. There's significant room for improvement here.`,
            `⚠️ A challenging session: ${roundedConf}% confidence on ${questionsCount} question(s). Don't give up, every rep counts.`,
            `🛠️ Rough session today (${roundedConf}% confidence). Review your answers and try these ${questionsCount} again.`,
            `🌱 This was a learning opportunity. ${roundedConf}% confidence on ${questionsCount} question(s) — let's focus on the basics.`
        ]));
    }

    // 2. Filler word feedback with exact numbers
    if (totalFillers === 0) {
        parts.push(pickRandom([
            `✅ Zero filler words detected — clean, crisp, and professional delivery.`,
            `✅ Absolutely flawless flow with 0 filler words. Excellent control!`,
            `✅ Not a single "um" or "like" detected. Your delivery was razor-sharp.`
        ]));
    } else if (fillerPct <= 2) {
        parts.push(pickRandom([
            `✅ Only ${totalFillers} filler word(s) (${fillerPct}%) — very polished and barely noticeable.`,
            `✅ Great job keeping fillers to a minimum (${totalFillers} total, ${fillerPct}%). Keep this up!`,
            `✅ Your speech is remarkably clean with just ${totalFillers} filler(s) (${fillerPct}%).`
        ]));
    } else if (fillerPct <= 5) {
        parts.push(pickRandom([
            `🗣️ ${totalFillers} filler word(s) (~${fillerPct}%) — slightly noticeable. Try deliberate pauses instead.`,
            `🗣️ You used ${totalFillers} fillers (${fillerPct}%). It's okay, but try taking a breath instead of saying "um".`,
            `🗣️ Minor hesitations detected (${totalFillers} fillers, ${fillerPct}%). Slow down slightly to think ahead.`
        ]));
    } else if (fillerPct <= 10) {
        parts.push(pickRandom([
            `⚠️ ${totalFillers} fillers at ${fillerPct}% of your speech. Interviewers will notice this. Practice silent pausing.`,
            `⚠️ High filler rate: ${totalFillers} words (${fillerPct}%). This can distract from your actual answers.`,
            `⚠️ You lean on fillers quite a bit (${fillerPct}% of words, ${totalFillers} total). Focus heavily on pausing next session.`
        ]));
    } else {
        parts.push(pickRandom([
            `🚨 Severe filler usage: ${totalFillers} words (${fillerPct}%). Focus entirely on shorter, decisive sentences.`,
            `🚨 Too many fillers (${totalFillers} total, ${fillerPct}%). This compromises your perceived confidence. Slow way down.`,
            `🚨 Filler overload (${fillerPct}% of your speech was padding, ${totalFillers} words). Practice speaking in short bursts.`
        ]));
    }

    // 3. Pace feedback with exact WPM
    if (roundedWpm === 0) {
        parts.push(`⏱️ Pace could not be measured accurately — try speaking clearly for a bit longer.`);
    } else if (roundedWpm < 100) {
        parts.push(pickRandom([
            `🐢 Sluggish pace at ${roundedWpm} WPM. Target 130–160 WPM to sound more naturally engaged.`,
            `🐢 At ${roundedWpm} WPM, your delivery is very slow. Try to inject a bit more energy.`,
            `🐢 ${roundedWpm} WPM is too hesitant. Practice speaking with a bit more fluidity.`
        ]));
    } else if (roundedWpm < 120) {
        parts.push(pickRandom([
            `📻 You spoke at ${roundedWpm} WPM — slightly slow but acceptable. A bit more tempo will improve your flow.`,
            `📻 At ${roundedWpm} WPM, you sound thoughtful, but perhaps a bit reserved. Don't be afraid to pick up the pace slightly.`,
            `📻 ${roundedWpm} WPM is okay, but leaning towards slow. Target the 130+ range for dynamic delivery.`
        ]));
    } else if (roundedWpm <= 160) {
        parts.push(pickRandom([
            `🎯 Excellent pacing at ${roundedWpm} WPM — right in the sweet spot!`,
            `🎯 You hit ${roundedWpm} WPM, which is the perfect conversational rhythm for interviews.`,
            `🎯 Great tempo control. ${roundedWpm} WPM demonstrates calm and confidence.`
        ]));
    } else if (roundedWpm <= 185) {
        parts.push(pickRandom([
            `🚀 Fast delivery at ${roundedWpm} WPM. Slow down slightly to let your heavy-hitting points sink in.`,
            `🚀 ${roundedWpm} WPM is quite brisk. Remember to breathe and give the interviewer time to process.`,
            `🚀 You're talking a bit fast (${roundedWpm} WPM). Nerves? Try to consciously slow your cadence.`
        ]));
    } else {
        parts.push(pickRandom([
            `⚡ Extremely rapid pace at ${roundedWpm} WPM. You must slow down to ensure clarity.`,
            `⚡ Rattling off at ${roundedWpm} WPM makes you sound nervous. Breathe and take your time.`,
            `⚡ ${roundedWpm} WPM is too fast for an interview setting. Consciously pause at every period.`
        ]));
    }

    // 4. Relevance with exact score
    if (roundedRel >= 80) {
        parts.push(pickRandom([
            `📌 Outstanding relevance (${roundedRel}%)! Your answers were perfectly aligned with the core questions.`,
            `📌 You nailed the topic (${roundedRel}% relevance). You addressed exactly what the interviewer would be looking for.`,
            `📌 Highly focused responses (${roundedRel}% relevance). Great job staying on track.`
        ]));
    } else if (roundedRel >= 60) {
        parts.push(pickRandom([
            `📝 Good overall relevance at ${roundedRel}%. Adding specific metric-driven examples could push this to Excellent.`,
            `📝 You answered the prompts well (${roundedRel}% relevance). Just try to tie your conclusions back to the original question more explicitly.`,
            `📝 Solid focus (${roundedRel}% relevance). Make sure every sentence serves the core premise of your answer.`
        ]));
    } else if (roundedRel >= 40) {
        parts.push(pickRandom([
            `🔍 Moderate relevance (${roundedRel}%). You drifted a bit. Ensure you include keywords tied directly to the prompt.`,
            `🔍 You somewhat answered the questions (${roundedRel}% relevance). Try the STAR method to stay more focused.`,
            `🔍 Fair relevance (${roundedRel}%). Often happens when rambling. Next time, state your thesis in the very first sentence.`
        ]));
    } else {
        parts.push(pickRandom([
            `🔴 Poor relevance (${roundedRel}%). Your answers seemed off-topic. Listen carefully to the prompt and answer it directly.`,
            `🔴 Low focus detected (${roundedRel}% relevance). It seems you didn't quite address what was being asked.`,
            `🔴 You struggled to stay on topic (${roundedRel}% relevance). Practice silently repeating the question to yourself before speaking.`
        ]));
    }

    // 5. Targeted closing tip
    if (totalFillers > 5 && roundedWpm > 170) {
        parts.push(pickRandom([
            `💡 Master Tip: High pace and fillers often go together. Slowing down naturally eliminates the "ums" because your brain has time to catch up.`,
            `💡 Focus Area: Take a deep breath before speaking. A controlled pace will instantly clean up those filler words.`
        ]));
    } else if (roundedConf < 50) {
        parts.push(pickRandom([
            `💡 Pro Tip: When you don't know the answer, structure is your best friend. Use Situation, Task, Action, Result (STAR).`,
            `💡 Action Item: Don't let a bad start ruin an answer. Pause, reset, and deliver what you do know confidently.`
        ]));
    } else if (totalFillers >= 8) {
        parts.push(pickRandom([
            `💡 Homework: Watch your video/listen to your audio. Hearing your own fillers is painful, but it's the fastest cure!`,
            `💡 Quick Fix: Replace every "um" with a silent 1-second pause. Silence sounds like thoughtfulness to an interviewer.`
        ]));
    } else if (roundedRel < 50) {
        parts.push(pickRandom([
            `💡 Core Strategy: Begin every answer by directly repeating the core of the question so you lock in the topic immediately.`,
            `💡 Tip: Rambling kills relevance. If you've made your point, just stop talking. Short and sweet beats deeply off-topic.`
        ]));
    } else if (roundedWpm < 110) {
        parts.push(pickRandom([
            `💡 Master Tip: A slow pace can imply a lack of confidence or knowledge. Practice speaking about your passions to find a more natural velocity.`,
            `💡 Focus Area: Try answering the exact same questions again, but challenge yourself to do it 20 seconds faster.`
        ]));
    } else {
        parts.push(pickRandom([
            `💡 Keep it up! Consistency builds the muscle memory required for real interview confidence.`,
            `💡 Great job today. To keep pushing, try practicing with the camera on to monitor your non-verbal communication as well.`,
            `💡 You're on the right track. Make sure you're regularly reviewing older sessions to ensure you don't regress on your metrics!`
        ]));
    }

    return parts.join(' ');
}
