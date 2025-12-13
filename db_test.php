<?php
/**
 * Database Connection Test Script
 * Use this to diagnose database connection issues on Heroku
 * 
 * Access via: https://your-app.herokuapp.com/db_test.php
 * 
 * IMPORTANT: Delete this file after testing for security!
 */

require_once 'config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <h1>Database Connection Test</h1>
    
    <h2>Environment Variables</h2>
    <div class="info">
        <strong>JAWSDB_URL:</strong> <?php echo getenv('JAWSDB_URL') ? 'SET' : 'NOT SET'; ?><br>
        <strong>CLEARDB_DATABASE_URL:</strong> <?php echo getenv('CLEARDB_DATABASE_URL') ? 'SET' : 'NOT SET'; ?><br>
        <strong>DATABASE_URL:</strong> <?php echo getenv('DATABASE_URL') ? 'SET' : 'NOT SET'; ?>
    </div>
    
    <?php if (getenv('JAWSDB_URL') || getenv('CLEARDB_DATABASE_URL')): ?>
        <div class="info">
            <strong>Database URL (masked):</strong><br>
            <?php 
            $url = getenv('JAWSDB_URL') ?: getenv('CLEARDB_DATABASE_URL');
            $masked = preg_replace('/(:\/\/)([^:]+):([^@]+)@/', '$1***:***@', $url);
            echo htmlspecialchars($masked);
            ?>
        </div>
    <?php endif; ?>
    
    <h2>Parsed Configuration</h2>
    <div class="info">
        <strong>Host:</strong> <?php echo defined('DB_HOST') ? htmlspecialchars(DB_HOST) : 'NOT DEFINED'; ?><br>
        <strong>Port:</strong> <?php echo defined('DB_PORT') ? DB_PORT : 'NOT DEFINED'; ?><br>
        <strong>Database:</strong> <?php echo defined('DB_NAME') ? htmlspecialchars(DB_NAME) : 'NOT DEFINED'; ?><br>
        <strong>User:</strong> <?php echo defined('DB_USER') ? htmlspecialchars(DB_USER) : 'NOT DEFINED'; ?><br>
        <strong>Password:</strong> <?php echo defined('DB_PASS') ? (strlen(DB_PASS) > 0 ? '***SET***' : 'EMPTY') : 'NOT DEFINED'; ?>
    </div>
    
    <h2>Connection Test</h2>
    <?php
    try {
        $dsn = getDatabaseDSN();
        echo '<div class="info"><strong>DSN:</strong> ' . htmlspecialchars($dsn) . '</div>';
        
        $pdo = new PDO(
            $dsn,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        
        echo '<div class="success">✓ Database connection successful!</div>';
        
        // Test query
        $stmt = $pdo->query("SELECT DATABASE() as db_name, VERSION() as version");
        $result = $stmt->fetch();
        echo '<div class="info">';
        echo '<strong>Connected to database:</strong> ' . htmlspecialchars($result['db_name']) . '<br>';
        echo '<strong>MySQL Version:</strong> ' . htmlspecialchars($result['version']);
        echo '</div>';
        
        // Check if users table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() > 0) {
            echo '<div class="success">✓ Users table exists</div>';
            
            // Count users
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
            $count = $stmt->fetch();
            echo '<div class="info"><strong>Total users in database:</strong> ' . $count['count'] . '</div>';
        } else {
            echo '<div class="error">✗ Users table does NOT exist. You need to run database.sql</div>';
        }
        
    } catch (PDOException $e) {
        echo '<div class="error">✗ Database connection failed!</div>';
        echo '<div class="error"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    }
    ?>
    
    <h2>Next Steps</h2>
    <div class="info">
        <ol>
            <li>If connection failed, check that the database add-on is provisioned: <code>heroku addons</code></li>
            <li>If users table doesn't exist, run your <code>database.sql</code> file on the Heroku database</li>
            <li>After testing, <strong>DELETE this file</strong> for security: <code>git rm db_test.php</code></li>
        </ol>
    </div>
</body>
</html>
