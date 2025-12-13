<?php
session_start();

// Load database configuration
require_once 'config.php';

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
                    header('Location: login.php?registered=1');
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

// Get error and form data from session if redirected
if (!$error && isset($_SESSION['register_error'])) {
    $error = $_SESSION['register_error'];
    unset($_SESSION['register_error']);
}

$formData = $_SESSION['register_form_data'] ?? [];
if (isset($_SESSION['register_form_data'])) {
    unset($_SESSION['register_form_data']);
}

// If there's an error on POST, store in session and redirect
if ($error && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['register_error'] = $error;
    $_SESSION['register_form_data'] = [
        'fullname' => $fullname ?? '',
        'username' => $username ?? '',
        'email' => $email ?? ''
    ];
    header('Location: register.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="icon/tabs/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icon/tabs/favicon.svg" />
    <link rel="shortcut icon" href="icon/tabs/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icon/tabs/apple-touch-icon.png" />
    <link rel="manifest" href="icon/tabs/site.webmanifest" />
    <title>UM Skills Clinic | Register</title>
    <link rel="stylesheet" href="css/styles.css" />
  </head>
  <body>
    <div class="auth">
      <main class="auth-panel">
        <div style="width: 100%; max-width: 460px">
          <div class="card-auth">
            <h2>Create an account</h2>
            <p class="muted">Join UM Skills Clinic and start your learning journey</p>

            <form id="register-form" method="POST" action="register.php">
              <div id="register-error" class="form-alert <?php echo $error ? '' : 'hidden'; ?>">
                <?php echo htmlspecialchars($error); ?>
              </div>

              <div class="form-group">
                <label class="form-label" for="reg-fullname">Full Name</label>
                <div class="input-wrap">
                  <span class="input-icon"><img src="icon/user.png" alt="User" /></span>
                  <input class="input" id="reg-fullname" name="fullname" type="text" placeholder="John Doe" value="<?php echo htmlspecialchars($formData['fullname'] ?? ''); ?>" required />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="reg-username">Username</label>
                <div class="input-wrap">
                  <span class="input-icon"><img src="icon/user.png" alt="Username" /></span>
                  <input class="input" id="reg-username" name="username" type="text" placeholder="johndoe123" value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>" required />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="reg-email">Email Address</label>
                <div class="input-wrap">
                  <span class="input-icon"><img src="icon/mail.png" alt="Email" /></span>
                  <input class="input" id="reg-email" name="email" type="email" placeholder="you@university.edu" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="reg-password">Password</label>
                <div class="input-wrap">
                  <span class="input-icon"><img src="icon/lock.png" alt="Password" /></span>
                  <input class="input" id="reg-password" name="password" type="password" placeholder="At least 8 characters" required />
                  <button class="toggle-visibility" type="button" id="register-toggle">
                    <img src="icon/eye.png" alt="Show password" id="register-toggle-icon" />
                  </button>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="reg-confirm">Confirm Password</label>
                <div class="input-wrap">
                  <span class="input-icon"><img src="icon/lock.png" alt="Password" /></span>
                  <input class="input" id="reg-confirm" name="confirm_password" type="password" placeholder="Re-enter your password" required />
                  <button class="toggle-visibility" type="button" id="register-toggle-confirm">
                    <img src="icon/eye.png" alt="Show password" id="register-toggle-confirm-icon" />
                  </button>
                </div>
              </div>

              <label class="terms" for="reg-terms">
                <input class="checkbox" id="reg-terms" name="terms" type="checkbox" value="1" />
                <span>
                  I agree to the <a class="text-link" href="#">Terms of Service</a> and
                  <a class="text-link" href="#">Privacy Policy</a>.
                </span>
              </label>

              <button class="btn btn-primary btn-block" id="register-submit" type="submit">Create Account</button>
            </form>

            <p class="footer-note">
              Already have an account?
              <a class="text-link" href="login.php">Sign in</a>
            </p>
          </div>
        </div>
      </main>

      <aside class="auth-panel branding secondary">
        <a class="brand" href="index.html">
          <img src="images/university-of-mindanao-logo.png" alt="UM Logo" width="56" height="56" />
          <div>
            <h1>UM Skills Clinic</h1>
            <p>Training & Development</p>
          </div>
        </a>

        <div class="auth-copy">
          <h2 style="margin: 0; font-size: 48px">Start your skill-building journey today</h2>
          <p style="margin: 0; color: rgba(0, 0, 0, 0.8); line-height: 1.6">
            Create your account to access exclusive workshops, earn certificates, and join a thriving community of
            learners committed to growth.
          </p>

          <div class="step-list">
            <div class="step">
              <span class="step-number">1</span>
              <div>
                <h4>Create Your Profile</h4>
                <p>Set up your account in seconds.</p>
              </div>
            </div>
            <div class="step">
              <span class="step-number">2</span>
              <div>
                <h4>Browse Workshops</h4>
                <p>Explore our diverse learning catalog.</p>
              </div>
            </div>
            <div class="step">
              <span class="step-number">3</span>
              <div>
                <h4>Start Learning</h4>
                <p>Gain skills that advance your career.</p>
              </div>
            </div>
          </div>
        </div>

        <p class="small-text" style="color: rgba(0, 0, 0, 0.7)">© 2025 UM Skills Clinic. All rights reserved.</p>
      </aside>
    </div>

    <script src="js/script.js"></script>
  </body>
</html>
