<?php
/**
 * Database Setup Script
 * This script will automatically create the database schema
 * 
 * Access via: https://your-app.herokuapp.com/setup_database.php
 * 
 * IMPORTANT: Delete this file after running for security!
 */

require_once 'config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
        h2 { color: #333; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Database Setup</h1>
    
    <?php
    // Check if already set up
    $alreadySetup = false;
    try {
        $pdo = new PDO(
            getDatabaseDSN(),
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() > 0) {
            $alreadySetup = true;
        }
    } catch (PDOException $e) {
        // Connection failed, will show error below
    }
    
    if ($alreadySetup) {
        echo '<div class="warning">';
        echo '<strong>⚠ Database already set up!</strong><br>';
        echo 'The users table already exists. If you want to recreate it, you can drop it first.';
        echo '</div>';
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
        try {
            $pdo = new PDO(
                getDatabaseDSN(),
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            
            // Create the users table directly
            $createTableSQL = "
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    fullname VARCHAR(255) NOT NULL,
                    username VARCHAR(100) NOT NULL UNIQUE,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_username (username),
                    INDEX idx_email (email)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            
            $pdo->exec($createTableSQL);
            $executed = 1;
            
            // Verify table was created
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
            if ($stmt->rowCount() > 0) {
                echo '<div class="success">';
                echo '<strong>✓ Database setup successful!</strong><br>';
                echo '✓ Users table created successfully!<br>';
                echo 'You can now use the registration and login features.';
                echo '</div>';
            } else {
                throw new Exception("Table creation appeared to succeed but table was not found");
            }
            
        } catch (PDOException $e) {
            echo '<div class="error">';
            echo '<strong>✗ Database setup failed!</strong><br>';
            echo 'Error: ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<strong>✗ Setup failed!</strong><br>';
            echo 'Error: ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
    }
    ?>
    
    <h2>Setup Database Schema</h2>
    <div class="info">
        <p>This will create the necessary database tables for the application.</p>
        <p><strong>Note:</strong> This will only create tables if they don't already exist (using IF NOT EXISTS).</p>
    </div>
    
    <?php if (!$alreadySetup): ?>
    <form method="POST">
        <button type="submit" name="setup" value="1">Setup Database</button>
    </form>
    <?php else: ?>
    <div class="warning">
        <p>Database is already set up. If you need to recreate it, you'll need to drop the existing tables first.</p>
    </div>
    <?php endif; ?>
    
    <h2>Manual Setup (Alternative)</h2>
    <div class="info">
        <p>If the automatic setup doesn't work, you can manually run the SQL:</p>
        <ol>
            <li>Get your database URL:
                <pre>heroku config:get JAWSDB_URL</pre>
            </li>
            <li>Connect using MySQL client:
                <pre>mysql -h [host] -u [user] -p[password] [database] < database.sql</pre>
            </li>
            <li>Or use MySQL Workbench/phpMyAdmin with the connection details</li>
        </ol>
    </div>
    
    <h2>Security Note</h2>
    <div class="warning">
        <p><strong>⚠ IMPORTANT:</strong> Delete this file after setup for security!</p>
        <pre>git rm setup_database.php
git commit -m "Remove setup script"
git push heroku main</pre>
    </div>
</body>
</html>
