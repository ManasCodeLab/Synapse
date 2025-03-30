<?php
session_start();
$db = new PDO("mysql:host=localhost;dbname=synapse", "root", "");

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$post_id = $data['post_id'];
$user_id = $_SESSION['user_id'];

// Check if the user already liked the post
$stmt = $db->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->execute([$user_id, $post_id]);

if ($stmt->rowCount() > 0) {
    $db->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?")->execute([$user_id, $post_id]);
} else {
    $db->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)")->execute([$user_id, $post_id]);
}

// Get updated like count
$stmt = $db->prepare("SELECT COUNT(*) as count FROM likes WHERE post_id = ?");
$stmt->execute([$post_id]);
$like_count = $stmt->fetchColumn();

echo json_encode(["likes" => $like_count]);
