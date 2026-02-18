# VoxVeil - AI-Powered Interview Practice Platform 🎤✨

VoxVeil is a state-of-the-art web application designed to help individuals master their interview skills through AI-driven practice sessions. It features real-time voice recognition, advanced linguistic analytics, and a premium glassmorphism UI.

---

## 🚀 Key Features & Upgrades

### 🎨 Premium Design System
- **Login Redesigned**: Centered, full-screen optimized login and registration with professional micro-animations.
- **Glassmorphism UI**: High-end translucent cards, vibrant gradients, and a sleek dark mode theme.
- **Responsive Web Design (RWD)**: Fully optimized for Mobile, Tablet, and Desktop displays.

### 🛡️ Smart Navigation & Security
- **Access Guards**: Practice sessions are strictly protected; users are prompted to complete their profiles before starting.
- **Seamless Flow**: Instant redirections from Profile to Practice, and Practice to Progress boards.
- **Anti-Cache Tech**: Implemented time-based versioning to ensure the latest design is always served.

### 📊 Advanced Performance Analytics
- **Relevance Detection**: AI-powered scoring that evaluates how well your answers address the specific interview question.
- **Real-time Metrics**: Tracks Filler Words, Words Per Minute (WPM), Confidence Scores, and Question counts.
- **History Modal**: Compact history cards with a detailed "View Full Details" modal for deep-dive transcript reviews.

---

## 🏗️ Technical Stack

- **Frontend**: HTML5, Vanilla CSS, JavaScript (ES6+), jQuery
- **Backend**: PHP 8.x
- **Database**: SQLite (Zero-configuration, portable persistent storage)
- **Visualizations**: Chart.js
- **APIs**: Web Speech API (Real-time Speech-to-Text)

---

## 🛠️ Quick Installation (MacOS/Windows)

### 1. Clone the Repository
```bash
git clone https://github.com/ilmaanoor/voxveil.git
cd voxveil
```

### 2. Configure PHP (Windows Users)
Ensure `pdo_sqlite` and `sqlite3` extensions are enabled in your `php.ini` file. See `SETUP_GUIDE_FOR_WINDOWS.md` for a step-by-step checklist.

### 3. Start the Local Server
From the project root directory:
```bash
php -S localhost:8005
```

### 4. Access the Platform
Open your browser (Chrome/Edge recommended) and go to:
[http://localhost:8005/index.php](http://localhost:8005/index.php)

---

## 📁 Core File Architecture
- `index.php`: Login & Brand Showcase
- `form.php`: Multi-step User Profiling
- `practice.php`: Evaluation Arena (Voice/Text Input)
- `history.php`: Session Archive with Detailed Modal
- `progress.php`: Data Visualizations & ROI Tracking
- `php/db-operations.php`: Core Database Logic & Metrics Calculation

---

## 💡 Best Practices for Evaluators
- **Microphone**: Ensure you grant microphone permissions for the best voice experience.
- **Profile First**: Use the "Get Started" page to define your status (UG/PG/Professional) to get personalized AI questions.
- **Hard Refresh**: If you've made CSS changes, use `Cmd+Shift+R` (Mac) or `Ctrl+F5` (Windows) to instantly skip the cache.

---
© 2026 VoxVeil Development Team. All rights reserved.
