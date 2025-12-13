<?php
session_start();

// Database configuration - Update these with your actual database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'um_skills_clinic');
define('DB_USER', 'root');
define('DB_PASS', '');

// Initialize variables
$error = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email)) {
        $error = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($password)) {
        $error = 'Password is required.';
    } else {
        // Database connection
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            // Find user by email
            $stmt = $pdo->prepare("SELECT id, fullname, username, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_fullname'] = $user['fullname'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['logged_in'] = true;
                
                // Redirect to dashboard
                header('Location: dashboard.html');
                exit;
            } else {
                // Invalid credentials
                $error = 'Invalid email or password. Please try again.';
            }
        } catch (PDOException $e) {
            // Log error (in production, log to file instead of displaying)
            error_log("Database error: " . $e->getMessage());
            $error = 'Login failed. Please try again later.';
        }
    }
}

// If there's an error, redirect back to login.html with error message
if ($error && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['login_error'] = $error;
    $_SESSION['login_email'] = $email ?? '';
    header('Location: login.html');
    exit;
}
?>
