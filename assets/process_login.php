<?php
declare(strict_types=1);
ini_set('display_errors', '0');
error_reporting(0);

// Security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Content-Type: application/json");
header("Cache-Control: no-cache, no-store, must-revalidate");

$response = [
    'success' => false,
    'message' => 'Invalid email or password',
    'redirect' => '../dashboard.php' // Set default redirect
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException('Invalid request method');
    }

    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Please provide a valid email address');
    }

    if (empty($password)) {
        throw new RuntimeException('Please enter your password');
    }

    require __DIR__ . '/../includes/new-config.php';
    
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start([
            'cookie_httponly' => true,
            'cookie_secure' => isset($_SERVER['HTTPS']),
            'use_strict_mode' => true
        ]);
        
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['last_activity'] = time();

        $response['success'] = true;
        $response['message'] = 'Login successful';
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $response['message'] = 'A system error occurred';
} catch (RuntimeException $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;