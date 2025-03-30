<?php
if (isset($_COOKIE['user_logged_in']) && $_COOKIE['user_logged_in'] === 'true' && 
isset($_COOKIE['user_id'])) {
header('Location: dashboard.php');
exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Synapse AI</title>
    <style>
        :root {
            --bg-primary: #0a0b0e;
            --bg-secondary: #12141a;
            --accent-primary: #6366f1;
            --accent-secondary: #818cf8;
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --glass-bg: rgba(18, 20, 26, 0.7);
            --error-color: #ef4444;
            --success-color: #22c55e;
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

        .nav {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 1rem 2rem;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav .logo {
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            margin-left: 10px;
            background: linear-gradient(to right, var(--text-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-auth {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            margin-right: 2rem;
        }

        .nav-auth a {
            color: var(--text-primary);
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s ease;
            white-space: nowrap;
            padding: 0.5rem;
        }

        .nav-auth a:hover {
            color: var(--accent-secondary);
        }

        .login-container {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            margin-top: 80px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .login-header p {
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
            border-radius: 0.5rem;
            border: 1px solid var(--accent-primary);
            background: var(--bg-secondary);
            color: var(--text-primary);
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent-secondary);
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            font-weight: 500;
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

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-footer a {
            color: var(--accent-secondary);
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            text-align: center;
            animation: fadeIn 0.3s ease-in-out;
        }

        .alert-error {
            background: rgba(220, 38, 38, 0.1);
            color: var(--error-color);
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .is-invalid {
            border-color: var(--error-color) !important;
        }

        .invalid-feedback {
            color: var(--error-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
<nav class="nav">
    <a href="/" class="logo">Synapse AI</a>
    <div class="nav-auth">
        <a href="login.php">Login</a>
        <a href="signup.php">Sign Up</a>
    </div>
</nav>

<div class="login-container">
    <div class="login-header">
        <h1>Welcome Back</h1>
        <p>Sign in to continue to Synapse AI</p>
    </div>
    
    <div id="alertContainer"></div>
    
    <form id="loginForm" action="assets/process_login.php" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Sign In</button>
    </form>
    
    <div class="login-footer">
        <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        <a href="forgot-password.php">Forgot your password?</a>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Clear previous alerts and errors
    document.getElementById('alertContainer').innerHTML = '';
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Signing in...';
    
    try {
        const formData = new URLSearchParams(new FormData(this));
        
        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formData
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response from server');
        }
        
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.message || 'Request failed');
        }
        
        if (result.success) {
            // Show success message
            showAlert('success', result.message);
            
            // Redirect after short delay
            setTimeout(() => {
                window.location.href = result.redirect || '../dashboard.php';
            }, 1000);
        } else {
            // Show error message
            showAlert('error', result.message);
            
            // Highlight error fields if provided
            if (result.errors) {
                Object.entries(result.errors).forEach(([field, message]) => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = message;
                        input.parentNode.appendChild(errorDiv);
                    }
                });
            }
        }
    } catch (error) {
        console.error('Login Error:', error);
        showAlert('error', error.message || 'An unexpected error occurred');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Sign In';
    }
});

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertContainer.appendChild(alertDiv);
    
    // Scroll to alert
    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>
</body>
</html>


