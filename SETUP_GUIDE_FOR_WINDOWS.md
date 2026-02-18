# 🎤 VoxVeil - Windows Setup Guide

Hey! This guide will help you get VoxVeil running on your Windows laptop in just a few simple steps.

## 📦 What You Need to Install

### 1. GitHub Desktop (to download the code)
1. Go to: https://desktop.github.com/
2. Click **"Download for Windows"**
3. Install it with default settings
4. Sign in with your GitHub account

### 2. PHP (to run the backend)
1. Go to: https://windows.php.net/download/
2. Download **"VS16 x64 Thread Safe"** ZIP (latest version, PHP 8.x)
3. Extract the ZIP to `C:\php`
4. **Add PHP to your PATH:**
   - Press `Windows + R`
   - Type: `sysdm.cpl` and press Enter
   - Click **"Advanced"** tab → **"Environment Variables"**
   - Under "System variables", find **"Path"** → Click **"Edit"**
   - Click **"New"** → Type: `C:\php`
   - Click **"OK"** on all windows
5. **Configure PHP (CRITICAL for Database):**
   - Go to `C:\php`
   - Find `php.ini-development` and rename it to `php.ini`
   - Open `php.ini` in Notepad
   - Search for `;extension=pdo_sqlite` and remove the `;` at the beginning
   - Search for `;extension=sqlite3` and remove the `;` at the beginning
   - Search for `;extension_dir = "ext"` and remove the `;`. Ensure it says `extension_dir = "ext"`
   - Save and Close.
6. **Test it works:**
   - Open Command Prompt (search "cmd")
   - Type: `php -v`
   - You should see the PHP version ✅

### 3. VS Code (optional, but recommended)
1. Go to: https://code.visualstudio.com/
2. Download and install for Windows

---

## 🚀 Getting the VoxVeil Project

### Method 1: GitHub Desktop (Recommended)
1. Open **GitHub Desktop**
2. Click **"File"** → **"Clone Repository"**
3. Click the **"URL"** tab
4. Paste: `https://github.com/ilmaanoor/voxveil`
5. Click **"Clone"**

### Method 2: Direct ZIP Download (No Extra Tools)
1. Open this link in Chrome/Edge: [https://github.com/ilmaanoor/voxveil](https://github.com/ilmaanoor/voxveil)
2. Click the green **"<> Code"** button
3. Click **"Download ZIP"**
4. Open your "Downloads" folder and **Right-click** on `voxveil-main.zip` → Choose **"Extract All..."**
5. Move the extracted folder to your Desktop or Documents.

---

## ⚙️ CRITICAL: Enable Database Support
Since her friend already has PHP 8.3 (`php -v`), she **MUST** do this one step or the website will show errors:

1.  Open your **PHP folder** (usually `C:\php`)
2.  Open the file named **`php.ini`** in Notepad.
3.  Search for these 3 specific lines and **remove the semicolon `;`** at the start of each:
    - `;extension=pdo_sqlite`  ➔  `extension=pdo_sqlite`
    - `;extension=sqlite3`     ➔  `extension=sqlite3`
    - `;extension_dir = "ext"` ➔  `extension_dir = "ext"`
4.  **Save and Close.**

---

## ▶️ Running VoxVeil
1. Open **Command Prompt** (search "cmd" in Windows)
2. Type `cd` followed by the path where she saved the folder. E.g.:
   ```bash
   cd C:\Users\Ilmaa Noor\Downloads\voxveil-main
   ```
3. Start the server:
   ```bash
   php -S localhost:8005
   ```
4. Open the browser and go to: **[http://localhost:8005/index.php](http://localhost:8005/index.php)**

---

## ▶️ Running VoxVeil

### Method 1: Using VS Code Terminal (Easiest)
1. In VS Code, click **"Terminal"** → **"New Terminal"**
2. Type this command:
   ```bash
   php -S localhost:8000
   ```
3. You should see:
   ```
   PHP Development Server started at http://localhost:8000
   ```
4. Open your browser (Chrome or Edge)
5. Go to: **http://localhost:8000/index.php**
6. VoxVeil is now running! 🎉

### Method 2: Using Command Prompt
1. Press `Windows + R`
2. Type: `cmd` and press Enter
3. Navigate to your project:
   ```bash
   cd C:\Users\YourName\Documents\voxveil
   ```
4. Start the server:
   ```bash
   php -S localhost:8005
   ```
5. Open browser → Go to: **http://localhost:8005/index.php**

---

## 🎯 Using VoxVeil

### First Time:
1. Click **"Register"** on the login page
2. Enter any email (e.g., `test@example.com`)
3. Create a password (minimum 6 characters)
4. Click **"Create Account"**

### Fill Your Profile:
1. After login, click **"Get Started"**
2. Fill the 3-step form:
   - Name and education level
   - Field of study/work
   - Interview purpose
3. Click **"Submit"**

### Practice Interview:
1. Go to **"Practice"** page
2. Click **"🎤 Start Speaking"**
3. **Allow microphone access** when browser asks
4. Answer the interview questions!
5. Your speech will be transcribed in real-time
6. See your metrics: filler words, WPM, confidence score

### View Progress:
1. Go to **"Progress"** page to see charts
2. Go to **"History"** page to review past sessions

---

## 🛠️ Troubleshooting

### "php is not recognized" Error
- PHP is not in your PATH
- Redo Step 2 above (Add PHP to PATH)
- Restart Command Prompt/VS Code after adding to PATH

### Microphone Not Working
- Use **Chrome** or **Edge** browser (Firefox doesn't support Web Speech API well)
- Click the microphone icon in the browser address bar and allow access
- Check Windows microphone settings

### Page Shows "Not Found"
- Make sure you go to `localhost:8000/index.php` (not just `localhost:8000`)
- Check that the PHP server is running in the terminal

### Database Errors
- The database file (`voxveil.db`) is created automatically
- Make sure the `database/` folder exists in your project

---

## 🛑 Stopping the Server

When you're done:
1. Go to the terminal where PHP is running
2. Press **Ctrl + C**
3. The server will stop

---

## 📚 Quick Reference

| Action | Command |
|--------|---------|
| Start Server | `php -S localhost:8000` |
| Stop Server | Press `Ctrl + C` |
| Open Website | `http://localhost:8000/index.php` |
| Check PHP | `php -v` |

---

## 💡 Tips

- Use Chrome or Edge for best voice recognition
- Practice in a quiet environment
- Allow microphone access when prompted
- Track your progress over multiple sessions

---

## ❓ Need Help?

If something doesn't work:
1. Make sure PHP is installed: open cmd and type `php -v`
2. Make sure you're in the correct folder when running the server
3. Use Chrome or Edge browser
4. Check that all files are in the project folder

Good luck with your interview practice! 🚀✨
