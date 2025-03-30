<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); 
define('DB_PASS', '');
define('DB_NAME', 'synapse');

// Establish database connection
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Application configuration
define('SITE_NAME', 'Synapse AI');
define('SITE_URL', 'http://localhost/synapse-ai'); // Update this with your domain
define('ADMIN_EMAIL', 'admin@synapseai.com');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security configuration
define('HASH_COST', 12); // For password hashing

// API configuration 
define('API_KEY', 'your_api_key_here');
define('API_SECRET', 'your_api_secret_here');

// Time zone
date_default_timezone_set('UTC');

// Define common functions
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generate_token() {
    return bin2hex(random_bytes(32));
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect($location) {
    header("Location: $location");
    exit();
}
