<?php
session_start();

// Database configuration - Update these with your actual database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'um_skills_clinic');
define('DB_USER', 'root');
define('DB_PASS', '');

// Initialize variables
$error = '';
$success = false;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']) ? true : false;

    // Validation
    if (empty($fullname)) {
        $error = 'Full name is required.';
    } elseif (empty($username)) {
        $error = 'Username is required.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username can only contain letters, numbers, and underscores.';
    } elseif (empty($email)) {
        $error = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($password)) {
        $error = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (!$terms) {
        $error = 'Please agree to the Terms of Service and Privacy Policy.';
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

            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username already exists. Please choose a different one.';
            } else {
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Email address is already registered. Please use a different email or try logging in.';
                } else {
                    // Hash password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Insert user into database
                    $stmt = $pdo->prepare("INSERT INTO users (fullname, username, email, password, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->execute([$fullname, $username, $email, $hashedPassword]);

                    $success = true;
                    $_SESSION['registration_success'] = true;
                    $_SESSION['registered_email'] = $email;
                    
                    // Redirect to login page
                    header('Location: login.html?registered=1');
                    exit;
                }
            }
        } catch (PDOException $e) {
            // Log error (in production, log to file instead of displaying)
            error_log("Database error: " . $e->getMessage());
            $error = 'Registration failed. Please try again later.';
        }
    }
}

// If there's an error, redirect back to register.html with error message
if ($error && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['register_error'] = $error;
    $_SESSION['register_form_data'] = [
        'fullname' => $fullname ?? '',
        'username' => $username ?? '',
        'email' => $email ?? ''
    ];
    header('Location: register.html');
    exit;
}
?>
