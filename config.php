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
$dbUrl = getenv('JAWSDB_URL') ?: getenv('CLEARDB_DATABASE_URL') ?: getenv('DATABASE_URL');

if ($dbUrl) {
    // Parse database URL from Heroku
    $dbConfig = parseDatabaseUrl($dbUrl);
    if ($dbConfig) {
        define('DB_HOST', $dbConfig['host'] . ($dbConfig['port'] != 3306 ? ':' . $dbConfig['port'] : ''));
        define('DB_NAME', $dbConfig['name']);
        define('DB_USER', $dbConfig['user']);
        define('DB_PASS', $dbConfig['pass']);
    } else {
        // Fallback to individual environment variables
        define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
        define('DB_NAME', getenv('DB_NAME') ?: 'um_skills_clinic');
        define('DB_USER', getenv('DB_USER') ?: 'root');
        define('DB_PASS', getenv('DB_PASS') ?: '');
    }
} else {
    // Local development - use environment variables or defaults
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_NAME', getenv('DB_NAME') ?: 'um_skills_clinic');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: '');
}
