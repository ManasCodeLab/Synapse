<?php
session_start();
require_once 'includes/new-config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['research_paper'])) {
    $file = $_FILES['research_paper'];
    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

    if (in_array($file['type'], $allowedTypes)) {
        $uploadDir = 'uploads/research/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $stmt = $conn->prepare("INSERT INTO research_papers (user_id, title, file_path, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$_SESSION['user_id'], $_POST['paper_title'], $fileName]);
            $successMessage = "Research paper created successfully!";
        } else {
            $errorMessage = "Failed to upload file";
        }
    } else {
        $errorMessage = "Only PDF and DOC/DOCX files are allowed";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $stmt = $conn->prepare("UPDATE users SET name = ?, bio = ?, institution = ? WHERE id = ?");
    if ($stmt->execute([$_POST['name'], $_POST['bio'], $_POST['institution'], $_SESSION['user_id']])) {
        $_SESSION['user_name'] = $_POST['name'];
        $successMessage = "Profile updated successfully!";
    } else {
        $errorMessage = "Failed to update profile";
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM research_papers WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$papers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Research Profile</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <nav class="nav">
        <a href="/" class="logo">Synapse AI</a>
        <div class="nav-auth">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="research.php">Research</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?></h1>

        <?php if (isset($successMessage)): ?>
            <div class="alert success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        
        <?php if (isset($errorMessage)): ?>
            <div class="alert error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <div class="research-section">
            <h2>Research Papers</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="paper_title">Paper Title:</label>
                    <input type="text" id="paper_title" name="paper_title" required>
                </div>

                <div class="form-group">
                    <label for="research_paper">Upload Paper (PDF/DOC):</label>
                    <input type="file" id="research_paper" name="research_paper" accept=".pdf,.doc,.docx" required>
                </div>

                <button type="submit" class="btn">Upload Paper</button>
            </form>

            <div class="papers-list">
                <h3>Your Papers</h3>
                <?php if (empty($papers)): ?>
                    <p>No papers created yet.</p>
                <?php else: ?>
                    <?php foreach ($papers as $paper): ?>
                        <div class="paper-item">
                            <div>
                                <strong><?php echo htmlspecialchars($paper['title']); ?></strong>
                                <div>created: <?php echo date('M d, Y', strtotime($paper['created_at'])); ?></div>
                            </div>
                            <a href="uploads/research/<?php echo htmlspecialchars($paper['file_path']); ?>" class="btn" download>Download</a>
                        <a href="delete_paper.php?id=<?php echo $paper['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this paper?');">Delete</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <script>
// Update paper-item elements to use btn-group
document.addEventListener('DOMContentLoaded', function() {
    const paperItems = document.querySelectorAll('.paper-item');
    paperItems.forEach(item => {
        const buttons = item.querySelectorAll('.btn');
        if (buttons.length > 1) {
            const btnGroup = document.createElement('div');
            btnGroup.className = 'btn-group';
            buttons.forEach(btn => {
                item.removeChild(btn);
                btnGroup.appendChild(btn);
            });
            item.appendChild(btnGroup);
        }
    });
});
</script>

</body>
</html>

