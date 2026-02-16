# 📦 FOR YOU (Madhumita) - How to Send Files to Your Friend

## Step 1: You Already Have the ZIP File!
The project is ready to send - you should have received `voxveil-project.zip`

## Step 2: Send to Your Friend
You can send the ZIP file via:
- **Email** (if under 25MB)
- **Google Drive** - Upload and share link
- **WhatsApp** 
- **USB Drive**
- **WeTransfer**

## Step 3: Send to Your Friend
You can send `voxveil.zip` via:
- **Email** (if under 25MB)
- **Google Drive** - Upload and share link
- **WhatsApp** 
- **USB Drive**
- **WeTransfer**

---

# 💻 FOR YOUR FRIEND - Windows Setup Guide

## What Your Friend Needs to Install

### 1. Install VS Code (Code Editor)
1. Go to: https://code.visualstudio.com/
2. Click **"Download for Windows"**
3. Run the downloaded `.exe` file
4. Install with default settings ✅

### 2. Install PHP (Required for Backend)
1. Go to: https://windows.php.net/download/
2. Download **"VS16 x64 Thread Safe"** ZIP (latest version)
3. Extract the ZIP file to `C:\php`
4. Add PHP to PATH:
   - Press `Windows + R`
   - Type: `sysdm.cpl` and press Enter
   - Click **"Environment Variables"**
   - Under "System variables", find **"Path"**
   - Click **"Edit"** → **"New"**
   - Add: `C:\php`
   - Click **"OK"** on all windows

5. Test PHP installation:
   - Open **Command Prompt** (search "cmd")
   - Type: `php -v`
   - You should see PHP version ✅

---

## Setting Up VoxVeil Project

### Step 1: Extract Project Files
1. Your friend receives `voxveil.zip`
2. Right-click → **"Extract All"**
3. Extract to easy location like:
   ```
   C:\Users\YourName\Desktop\voxveil
   ```

### Step 2: Open in VS Code
1. Open **VS Code**
2. Click **"File"** → **"Open Folder"**
3. Select the `voxveil` folder
4. Click **"Select Folder"**

### Step 3: Check Folder Structure
Make sure the folder looks like this:
```
voxveil/
├── index.php
├── home.php
├── form.php
├── practice.php
├── progress.php
├── history.php
├── css/
│   └── styles.css
├── js/
│   ├── validation.js
│   ├── events.js
│   ├── practice.js
│   └── progress.js
├── php/
│   ├── config.php
│   ├── auth.php
│   ├── session.php
│   ├── form-handler.php
│   ├── practice-handler.php
│   └── db-operations.php
└── database/
```

### Step 4: Create Database Folder
1. In VS Code, right-click on `voxveil` folder
2. Click **"New Folder"**
3. Name it: `database`
4. The database file will be created automatically when you run the app!

---

## Running VoxVeil on Windows

### Method 1: Using VS Code Terminal (Recommended)

1. In VS Code, click **"Terminal"** → **"New Terminal"**
2. Type this command and press Enter:
   ```bash
   php -S localhost:8000
   ```
3. You should see:
   ```
   PHP 8.x Development Server (http://localhost:8000) started
   ```
4. Open your browser (Chrome or Edge)
5. Go to: `http://localhost:8000/index.php`
6. The VoxVeil website will open! 🎉

### Method 2: Using Command Prompt

1. Press `Windows + R`
2. Type: `cmd` and press Enter
3. Navigate to project folder:
   ```bash
   cd C:\Users\YourName\Desktop\voxveil
   ```
4. Start the server:
   ```bash
   php -S localhost:8000
   ```
5. Open browser and go to: `http://localhost:8000/index.php`

---

## Using VoxVeil

### First Time Setup:
1. Click **"Register"** tab
2. Enter any email (example: `test@test.com`)
3. Enter any password (minimum 6 characters)
4. Click **"Create Account"**

### Fill the Form:
1. After registration, you'll see the Home page
2. Click **"Get Started"** or **"Fill Form"**
3. Fill in:
   - Your name
   - Education (UG/PG/Employee)
   - Field (BCA/Physics/etc.)
   - Purpose (graduation interviews/company switch/etc.)
4. Click **"Submit"**

### Practice Interview:
1. Go to **Practice** page
2. Click **"🎤 Start Speaking"**
3. **Allow microphone** when browser asks
4. Start answering the interview questions!
5. Your speech will be transcribed in real-time
6. See your metrics (filler words, WPM, confidence)

---

## Troubleshooting

### "php is not recognized" Error
- PHP is not installed or not in PATH
- Reinstall PHP and add to PATH (see Step 2 above)

### Microphone Not Working
- Use **Chrome** or **Edge** browser (not Firefox)
- Allow microphone permission when browser asks
- Check Windows microphone settings

### Page Shows "Not Found"
- Make sure you're going to `localhost:8000/index.php` (not just `localhost:8000`)
- Check that PHP server is running in terminal

### Database Errors
- The database file (`voxveil.db`) will be created automatically
- Make sure the `database/` folder exists in your project

---

## Stopping the Server

When you're done:
1. Go to the terminal/command prompt where PHP is running
2. Press **Ctrl + C**
3. The server will stop

---

## Quick Reference

| What | Command |
|------|---------|
| Start Server | `php -S localhost:8000` |
| Stop Server | Press `Ctrl + C` |
| Open Website | `http://localhost:8000/index.php` |
| Check PHP | `php -v` |

---

## Need Help?

- Make sure all files are in correct folders
- Check that PHP is installed: open cmd and type `php -v`
- Use Chrome or Edge browser for best experience
- Allow microphone access for speech recognition

Good luck! 🚀
