<?php
session_start();
$db = new PDO("mysql:host=localhost;dbname=synapse", "root", "");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$post_id = $_POST['post_id'];
$content = $_POST['content'];
$user_id = $_SESSION['user_id'];

$db->prepare("INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)")
   ->execute([$user_id, $post_id, $content]);

header("Location: research_feed.php");
