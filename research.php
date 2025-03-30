
<?php
session_start();
$db = new PDO("mysql:host=localhost;dbname=synapse", "root", "");

// Run the query safely
$stmt = $db->query("
    SELECT research_papers.*, users.name, 
        (SELECT COUNT(*) FROM likes WHERE post_id = research_papers.id) as like_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = research_papers.id) as comment_count
    FROM research_papers 
    JOIN users ON research_papers.user_id = users.id 
    ORDER BY research_papers.created_at DESC
");

// Check if the query was successful
if ($stmt) {
    $papers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "<p>Error fetching research papers!</p>";
    $papers = []; // Set an empty array to avoid foreach errors
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Feed - Synapse AI</title>
    <link href="assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <nav class="nav">
        <a href="/" class="logo">Synapse AI</a>
        <div class="nav-auth">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>
<div class="research-feed">
    <?php
    if (!empty($papers)):  // Ensure $papers is not empty before looping
        foreach ($papers as $paper):
    ?>
        <div class="post" data-post-id="<?= htmlspecialchars($paper['id']) ?>">
            <div class="post-header">
                <span class="post-author"><?= htmlspecialchars($paper['name']) ?></span>
                <span class="post-date"><?= date('M d, Y', strtotime($paper['created_at'])) ?></span>
            </div>
            <div class="post-content">
                <?= nl2br(htmlspecialchars($paper['content'])) ?>
            </div>
            <div class="post-actions">
                <button class="like-btn">
                    <i class="far fa-heart"></i>
                    <span class="like-count"><?= $paper['like_count'] ?></span> Likes
                </button>
                <button class="comment-btn">
                    <i class="far fa-comment"></i>
                    <span class="comment-count"><?= $paper['comment_count'] ?></span> Comments
                </button>
            </div>
        </div>
    <?php 
        endforeach;
    else:
        echo "<p>No research papers found.</p>";
    endif;
    ?>
</div>
</body>
</html>
