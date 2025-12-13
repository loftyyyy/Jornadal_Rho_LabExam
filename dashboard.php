<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Get user information from session
$userFullname = $_SESSION['user_fullname'] ?? 'User';
$userUsername = $_SESSION['user_username'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';
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
    <title>UM Skills Clinic | Dashboard</title>
    <link rel="stylesheet" href="css/styles.css" />
  </head>
  <body>
    <header class="site-header">
      <div class="container" style="padding: 16px 20px; display: flex; align-items: center; justify-content: space-between">
        <a class="brand" href="index.html">
          <img src="images/university-of-mindanao-logo.png" alt="UM Logo" width="42" height="42" />
          <div>
            <h1>UM Skills Clinic</h1>
            <p>Training & Development</p>
          </div>
        </a>
        <div class="header-actions">
          <span style="color: var(--muted-foreground); margin-right: 12px;"><?php echo htmlspecialchars($userFullname); ?></span>
          <a class="btn btn-outline" href="logout.php" style="text-decoration: none;">Log Out</a>
        </div>
      </div>
    </header>

    <main class="container" style="padding: 80px 20px; text-align: center;">
      <div class="card" style="max-width: 500px; margin: 0 auto;">
        <h2 style="margin: 0 0 16px; font-size: 32px; color: var(--primary);">Log In Successful</h2>
        <p style="margin: 16px 0 0; color: var(--muted-foreground);">Welcome, <?php echo htmlspecialchars($userFullname); ?>!</p>
      </div>
    </main>
  </body>
</html>
