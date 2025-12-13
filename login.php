<?php
session_start();

// Load database configuration
require_once 'config.php';

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
                getDatabaseDSN(),
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
                header('Location: dashboard.php');
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

// Get error from session if redirected
if (!$error && isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

$loginEmail = $_SESSION['login_email'] ?? '';
if (isset($_SESSION['login_email'])) {
    unset($_SESSION['login_email']);
}

$registered = isset($_GET['registered']) && $_GET['registered'] == '1';
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
    <title>UM Skills Clinic | Log In</title>
    <link rel="stylesheet" href="css/styles.css" />
  </head>
  <body>
    <div class="auth">
      <aside class="auth-panel branding">
        <a class="brand" href="index.html">
          <img src="images/university-of-mindanao-logo.png" alt="UM Logo" width="56" height="56" />
          <div>
            <h1>UM Skills Clinic</h1>
            <p>Training & Development</p>
          </div>
        </a>

        <div class="auth-copy">
          <h2 style="margin: 0; font-size: 48px">Welcome back to your learning journey</h2>
          <p style="margin: 0; color: rgba(255, 255, 255, 0.9); line-height: 1.6">
            Continue building the skills that matter. Access your workshops, track your progress, and connect with your
            learning community.
          </p>
          <div style="display: flex; align-items: center; gap: 12px; margin-top: 50px">
            <div class="avatar-stack">
              <span class="avatar">JD</span>
              <span class="avatar">SM</span>
              <span class="avatar">AL</span>
              <span class="avatar">RK</span>
            </div>
            <p style="margin: 0; font-size: 14px; color: rgba(255, 255, 255, 0.85)">
              Join <strong>500+ students</strong> actively learning
            </p>
          </div>
        </div>

        <p class="small-text" style="color: rgba(255, 255, 255, 0.75)">© 2025 UM Skills Clinic. All rights reserved.</p>
      </aside>

      <main class="auth-panel">
        <div style="width: 100%; max-width: 460px">
          <div class="card-auth">
            <h2>Welcome back</h2>
            <p class="muted">Sign in to access your learning dashboard</p>

            <form id="login-form" method="POST" action="login.php">
              <?php if ($registered): ?>
                <div class="form-alert" style="background: rgba(34, 197, 94, 0.1); color: #15803d; border-color: rgba(34, 197, 94, 0.3);">Account created successfully! Please sign in.</div>
              <?php endif; ?>
              <div id="login-error" class="form-alert <?php echo $error ? '' : 'hidden'; ?>">
                <?php echo htmlspecialchars($error); ?>
              </div>

              <div class="form-group">
                <label class="form-label" for="login-email">Email Address</label>
                <div class="input-wrap">
                  <span class="input-icon"><img src="icon/mail.png" alt="Email" /></span>
                  <input class="input" id="login-email" name="email" type="email" placeholder="you@university.edu" value="<?php echo htmlspecialchars($loginEmail); ?>" required />
                </div>
              </div>

              <div class="form-group">
                <div class="form-row">
                  <label class="form-label" for="login-password">Password</label>
                  <a class="small-text text-link" href="#" style="color: #a63005;">Forgot password?</a>
                </div>
                <div class="input-wrap">
                  <span class="input-icon"><img src="icon/lock.png" alt="Password" /></span>
                  <input class="input" id="login-password" name="password" type="password" placeholder="Enter your password" required />
                  <button class="toggle-visibility" type="button" id="login-toggle">
                    <img src="icon/eye.png" alt="Show password" id="login-toggle-icon" />
                  </button>
                </div>
              </div>

              <button class="btn btn-primary btn-block" id="login-submit" type="submit">Sign In</button>
            </form>

            <p class="footer-note">
              Don't have an account?
              <a class="text-link" href="register.php">Sign up</a>
            </p>
          </div>
        </div>
      </main>
    </div>

    <script src="js/script.js"></script>
  </body>
</html>
