<?php
/**
 * Configuration settings
 */

// Get the host name dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Application settings
define('APP_NAME', 'WebBlog223');
define('APP_URL', $protocol . $host); // Dynamic URL based on current host
define('API_BASE_URL', APP_URL . '/backend');

// JWT settings
define('JWT_SECRET', 'your_jwt_secret_key_here'); // Change this in production!
define('JWT_EXPIRATION', 3600); // Token expiration in seconds (1 hour)

// Session settings
define('SESSION_LIFETIME', 86400); // Session lifetime in seconds (24 hours)

// Define database connection info
define('DB_HOST', 'localhost');
define('DB_NAME', 'webblog223');
define('DB_USER', 'root');
define('DB_PASS', '123456'); // Set your database password if needed

// Error reporting settings
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Set your timezone

// CORS settings for API
header("Access-Control-Allow-Origin: " . APP_URL);
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
