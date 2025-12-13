<?php
/**
 * Database Configuration
 * Supports both local development and Heroku deployment
 */

// Function to parse database URL (for Heroku)
function parseDatabaseUrl($url) {
    if (empty($url)) {
        return null;
    }
    
    $parsed = parse_url($url);
    if (!$parsed) {
        return null;
    }
    
    return [
        'host' => $parsed['host'] ?? 'localhost',
        'port' => $parsed['port'] ?? 3306,
        'user' => $parsed['user'] ?? 'root',
        'pass' => $parsed['pass'] ?? '',
        'name' => isset($parsed['path']) ? ltrim($parsed['path'], '/') : 'um_skills_clinic'
    ];
}

// Get database configuration from environment variables (Heroku) or use defaults (local)
// Try JAWSDB_URL first (JawsDB), then CLEARDB_DATABASE_URL (ClearDB), then DATABASE_URL
$dbUrl = getenv('JAWSDB_URL');
if (!$dbUrl) {
    $dbUrl = getenv('CLEARDB_DATABASE_URL');
}
if (!$dbUrl) {
    $dbUrl = getenv('DATABASE_URL');
}

if ($dbUrl) {
    // Parse database URL from Heroku
    $dbConfig = parseDatabaseUrl($dbUrl);
    if ($dbConfig) {
        // Store host and port separately for PDO connection
        define('DB_HOST', $dbConfig['host']);
        define('DB_PORT', $dbConfig['port']);
        define('DB_NAME', $dbConfig['name']);
        define('DB_USER', $dbConfig['user']);
        define('DB_PASS', $dbConfig['pass']);
    } else {
        // Fallback to individual environment variables
        define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
        define('DB_PORT', getenv('DB_PORT') ?: 3306);
        define('DB_NAME', getenv('DB_NAME') ?: 'um_skills_clinic');
        define('DB_USER', getenv('DB_USER') ?: 'root');
        define('DB_PASS', getenv('DB_PASS') ?: '');
    }
} else {
    // Local development - use environment variables or defaults
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('DB_PORT') ?: 3306);
    define('DB_NAME', getenv('DB_NAME') ?: 'um_skills_clinic');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: '');
}

// Helper function to get PDO DSN string
function getDatabaseDSN() {
    $port = defined('DB_PORT') ? DB_PORT : 3306;
    return "mysql:host=" . DB_HOST . ";port=" . $port . ";dbname=" . DB_NAME . ";charset=utf8mb4";
}
