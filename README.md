# VoxVeil - Interview Practice Platform

## 🚀 Quick Setup

### 1. Database Setup
```sql
-- Open MySQL and run:
mysql -u root -p
source database/schema.sql
```

### 2. Configure Database
Edit `php/config.php` and update if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password
define('DB_NAME', 'voxveil_db');
```

### 3. Start PHP Server
```bash
cd /Users/madhumita/.gemini/antigravity/scratch/voxveil
php -S localhost:8000
```

### 4. Open in Browser
```
http://localhost:8000/index.php
```

## 📁 Project Structure

```
voxveil/
├── index.php           - Login/Registration page
├── home.php            - Home page with hero section
├── form.php            - User profile form (multi-step)
├── practice.php        - Interview practice with voice/text
├── progress.php        - Analytics and progress charts
├── history.php         - Session history
├── css/
│   └── styles.css      - Complete design system
├── js/
│   ├── validation.js   - Form validation with DOM
│   ├── events.js       - Event handlers (blur, focus, click, etc.)
│   ├── practice.js     - Web Speech API & recording
│   └── progress.js     - Chart.js visualizations
├── php/
│   ├── config.php      - Database connection
│   ├── session.php     - Session management
│   ├── auth.php        - Login/Register handlers (POST)
│   ├── form-handler.php - Profile save (GET/POST)
│   ├── practice-handler.php - Session save & questions
│   └── db-operations.php - CRUD operations
├── database/
│   └── schema.sql      - MySQL schema with AUTO_INCREMENT
└── assets/
    └── images/         - Generated images
```

## ✨ Features Implemented

### JavaScript & jQuery
✅ Event handling: blur, focus, click, dblclick, keypress
✅ Form validation using DOM constraints
✅ jQuery selectors and event functions
✅ JavaScript objects and arrays
✅ Accessing CSS from JavaScript
✅ 'this' keyword usage throughout

### PHP Backend
✅ Form handling with GET and POST
✅ PHP form validation
✅ Session management with timeout
✅ Cookie handling (Remember Me)
✅ MySQL database connection

### MySQL Database
✅ CREATE TABLE with AUTO_INCREMENT
✅ INSERT operations
✅ SELECT operations
✅ UPDATE operations
✅ DELETE operations
✅ DESCRIBE table functionality

### Design
✅ Responsive Web Design (RWD)
✅ Glassmorphism effects
✅ Dark theme with gradients
✅ Smooth animations
✅ Mobile-first approach

## 🎯 How to Use

1. **Register** - Create an account at index.php
2. **Login** - Sign in with your credentials
3. **Home** - View features and information
4. **Form** - Fill your profile (3-step form)
5. **Practice** - Start interview session with:
   - Voice input (Web Speech API)
   - Text input
   - Real-time transcript
   - Filler word detection
6. **Progress** - View analytics and charts
7. **History** - Review past sessions and retake

## 🛠️ Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript, jQuery
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Charts**: Chart.js
- **Fonts**: Google Fonts (Inter, Poppins)

## 💡 Tips

- Use Chrome or Edge for voice recognition
- Allow microphone access when prompted
- Practice in a quiet environment
- Track your progress over multiple sessions

## 🎨 Design Features

- Modern glassmorphism UI
- Vibrant gradient colors
- Dark mode theme
- Smooth micro-animations
- Fully responsive (mobile/tablet/desktop)

Enjoy practicing! 🎤✨
