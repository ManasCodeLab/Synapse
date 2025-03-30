<?php
declare(strict_types=1);
ini_set('display_errors', '0');
error_reporting(0);

// Clear output buffers
while (ob_get_level()) ob_end_clean();

// Set JSON headers
header("Content-Type: application/json");
header("Cache-Control: no-cache, no-store, must-revalidate");

$response = [
    'success' => false,
    'message' => 'An error occurred',
    'redirect' => ''
];

try {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException('Invalid request method', 405);
    }

    // Get and validate inputs
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $confirm_password = $data['confirm_password'] ?? '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        throw new RuntimeException('All fields are required', 400);
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Invalid email format', 400);
    }

    // Validate password match
    if ($password !== $confirm_password) {
        throw new RuntimeException('Passwords do not match', 400);
    }

    // Validate password strength
    if (strlen($password) < 8) {
        throw new RuntimeException('Password must be at least 8 characters', 400);
    }

    // Database operations
    require __DIR__.'/../includes/new-config.php';

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        throw new RuntimeException('Email already registered', 409);
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    if ($hashed_password === false) {
        throw new RuntimeException('Password hashing failed', 500);
    }

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
    if (!$stmt->execute([$name, $email, $hashed_password])) {
        throw new RuntimeException('Account creation failed', 500);
    }

    // Start secure session
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true
    ]);
    
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $conn->lastInsertId();
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    $response = [
        'success' => true,
        'message' => 'Account created successfully',
        'redirect' => 'dashboard.php'
    ];

} catch (RuntimeException $e) {
    $response['message'] = $e->getMessage();
    error_log("Signup Error: " . $e->getMessage());
} catch (PDOException $e) {
    $response['message'] = 'Database error occurred';
    error_log("Database Error: " . $e->getMessage());
} catch (Throwable $e) {
    $response['message'] = 'An unexpected error occurred';
    error_log("System Error: " . $e->getMessage());
}

echo json_encode($response);
exit;