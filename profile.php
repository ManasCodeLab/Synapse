<?php
session_start();
require_once 'includes/new-config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $institution = trim($_POST['institution'] ?? '');
    
    // Basic validation
    if (empty($name)) {
        $error = "Name is required";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, bio = ?, institution = ? WHERE id = ?");
            if ($stmt->execute([$name, $email, $bio, $institution, $_SESSION['user_id']])) {
                $_SESSION['user_name'] = $name;
                $success = "Profile updated successfully!";
            }
        } catch (PDOException $e) {
            $error = "Failed to update profile";
            error_log("Profile update error: " . $e->getMessage());
        }
    }
}

// Get current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Synapse AI</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    
<nav class="nav">
        <a href="/" class="logo">Synapse AI</a>
        <div class="nav-auth">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="chat.php">Synapse AI</a>    
                <a href="research.php">Research</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>



    <div class="container">
        <h1>Edit Profile</h1>

        <?php if (isset($error)): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="profile-form">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="bio">Bio:</label>
                    <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="institution">Institution:</label>
                    <input type="text" id="institution" name="institution" value="<?php echo htmlspecialchars($user['institution'] ?? ''); ?>">
                </div>

                <button type="submit" class="btn">Update Profile</button>
            </form>
        </div>
    </div>

    <script>
        // Simple form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!name) {
                e.preventDefault();
                alert('Name is required');
            } else if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
            }
        });
    </script>
</body>
</html>
