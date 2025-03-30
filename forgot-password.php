<?php
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Synapse AI</title>
    <style>
        :root {
            --bg-primary: #0a0b0e;
            --bg-secondary: #12141a;
            --accent-primary: #6366f1;
            --accent-secondary: #818cf8;
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --glass-bg: rgba(18, 20, 26, 0.7);
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reset-container {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 1rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .reset-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .reset-header h1 {
            margin-bottom: 0.5rem;
        }

        .reset-header p {
            color: var(--text-secondary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--accent-primary);
            border-radius: 0.5rem;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--accent-primary);
            color: var(--text-primary);
        }

        .btn-primary:hover {
            background: var(--accent-secondary);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .reset-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .reset-footer a {
            color: var(--accent-secondary);
            text-decoration: none;
        }

        .reset-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h1>Reset Password</h1>
            <p>Enter your email to receive reset instructions</p>
        </div>
        
        <form action="process_reset.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </form>
        
        <div class="reset-footer">
            <p>Remember your password? <a href="login.php">Sign in</a></p>
        </div>
    </div>
</body>
</html>
