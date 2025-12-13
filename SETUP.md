# How to Run This Project

## Prerequisites

1. **PHP** (version 7.4 or higher)
   - Download from: https://www.php.net/downloads.php
   - Make sure PHP is added to your system PATH
   - Verify installation: `php -v`

2. **MySQL/MariaDB**
   - Download MySQL: https://dev.mysql.com/downloads/installer/
   - Or use XAMPP/WAMP which includes both PHP and MySQL

3. **Web Server** (choose one):
   - **Option A**: PHP Built-in Server (easiest for development)
   - **Option B**: XAMPP/WAMP (includes Apache + MySQL + PHP)
   - **Option C**: Apache/Nginx (for production)

## Setup Steps

### Step 1: Set Up the Database

1. **Using MySQL Command Line:**
   ```bash
   mysql -u root -p < database.sql
   ```
   (Enter your MySQL root password when prompted)

2. **Using phpMyAdmin:**
   - Open phpMyAdmin in your browser (usually http://localhost/phpmyadmin)
   - Click on "SQL" tab
   - Copy and paste the contents of `database.sql`
   - Click "Go"

3. **Using MySQL Workbench:**
   - Open MySQL Workbench
   - Connect to your MySQL server
   - Open `database.sql` file
   - Execute the SQL script

### Step 2: Configure Database Credentials

The database credentials are already set in `login.php` and `register.php`:
- **Host**: `localhost`
- **Database**: `um_skills_clinic`
- **User**: `root`
- **Password**: `` (empty by default)

If your MySQL setup uses different credentials, update these files:
- `login.php` (lines 5-8)
- `register.php` (lines 5-8)

### Step 3: Start the Web Server

#### Option A: Using PHP Built-in Server (Recommended for Development)

1. Open PowerShell or Command Prompt
2. Navigate to the project directory:
   ```powershell
   cd "c:\Users\User\Documents\GitHub\Jornadal_Rho_LabExam"
   ```
3. Start the PHP server:
   ```powershell
   php -S localhost:8000
   ```
4. Open your browser and go to: **http://localhost:8000**

#### Option B: Using XAMPP

1. Start XAMPP Control Panel
2. Start Apache and MySQL services
3. Copy the project folder to `C:\xampp\htdocs\` (or your XAMPP htdocs directory)
4. Open your browser and go to: **http://localhost/Jornadal_Rho_LabExam**

#### Option C: Using WAMP

1. Start WAMP Server
2. Copy the project folder to `C:\wamp64\www\` (or your WAMP www directory)
3. Open your browser and go to: **http://localhost/Jornadal_Rho_LabExam**

## Testing the Application

1. **Home Page**: http://localhost:8000/index.html
2. **Register**: http://localhost:8000/register.php
3. **Login**: http://localhost:8000/login.php
4. **Dashboard**: http://localhost:8000/dashboard.php (after successful login)

## Troubleshooting

### Database Connection Error
- Make sure MySQL is running
- Verify database credentials in `login.php` and `register.php`
- Check that the database `um_skills_clinic` exists

### PHP Not Found
- Make sure PHP is installed and in your system PATH
- Verify with: `php -v`

### Port Already in Use
- If port 8000 is busy, use a different port:
  ```powershell
  php -S localhost:8080
  ```

### Session Errors
- Make sure PHP sessions are enabled (usually enabled by default)
- Check that the `session_start()` calls are at the top of PHP files

## Project Structure

```
Jornadal_Rho_LabExam/
├── css/
│   └── styles.css          # Main stylesheet
├── js/
│   └── script.js           # JavaScript functionality
├── icon/                   # Icons and favicons
├── images/                 # Images (logos, etc.)
├── index.html              # Home page
├── login.php               # Login page and backend
├── register.php            # Registration page and backend
├── dashboard.php           # Dashboard (after login, requires authentication)
├── logout.php              # Logout handler
└── database.sql            # Database schema
```
