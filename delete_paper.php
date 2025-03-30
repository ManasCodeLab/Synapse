<?php
session_start();
require_once 'includes/new-config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$paper_id = $_GET['id'] ?? null;

if (!$paper_id) {
    $_SESSION['error'] = "Invalid request!";
    header('Location: dashboard.php');
    exit;
}

try {
    $stmt = $conn->prepare("SELECT file_path, user_id FROM research_papers WHERE id = ?");
    $stmt->execute([$paper_id]);
    $paper = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paper || $paper['user_id'] != $_SESSION['user_id']) {
        $_SESSION['error'] = "You don't have permission to delete this paper";
        header('Location: dashboard.php');
        exit;
    }

    // Delete file from the server
    $filePath = 'uploads/research/' . $paper['file_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM research_papers WHERE id = ? AND user_id = ?");
    $stmt->execute([$paper_id, $_SESSION['user_id']]);

    $_SESSION['success'] = "Paper deleted successfully";
} catch (PDOException $e) {
    $_SESSION['error'] = "Failed to delete paper: " . $e->getMessage();
    error_log("Paper deletion error: " . $e->getMessage());
}

header('Location: dashboard.php');
exit;
?>
