<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Synapse AI</title>
    <style>
        :root {
            --primary: #6366f1;
            --error: #ef4444;
            --success: #22c55e;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #0a0b0e;
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: rgba(18, 20, 26, 0.7);
            backdrop-filter: blur(10px);
            margin-top: 80px;
            padding: 2rem;
            border-radius: 1rem;
            width: 100%;
            max-width: 400px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid var(--primary);
            background: #12141a;
            color: white;
        }
        .btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem;
            width: 100%;
            border-radius: 0.5rem;
            cursor: pointer;
        }
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--error);
            color: var(--error);
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
        }
        

body::-webkit-scrollbar {
    display: none;
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
    background: linear-gradient(to right, var(--text-primary), var(--accent-secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.nav-auth {
    display: flex;
    gap: 1.5rem;
    align-items: center;
    margin-right: 2rem;
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.nav-auth::-webkit-scrollbar {
    display: none;
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
    </style>
</head>
<body>
<?php include 'assets/header.php'; ?> 
    <div class="container">
        <div id="alertContainer"></div>
        
        <h1>Create Account</h1>
        <p>Join Synapse AI today</p>
        
        <form id="signupForm" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn">Create Account</button>
        </form>
        
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>

    <script>
    document.getElementById('signupForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const alertContainer = document.getElementById('alertContainer');
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Clear previous messages
        alertContainer.innerHTML = '';
        
        // Set loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating account...';
        
        try {
            const formData = new URLSearchParams(new FormData(form));
            
            const response = await fetch('assets/process_signup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success';
                alertDiv.textContent = result.message;
                alertContainer.appendChild(alertDiv);
                
                // Redirect after delay
                setTimeout(() => {
                    window.location.href = result.redirect || 'dashboard.php';
                }, 1500);
            } else {
                throw new Error(result.message || 'Registration failed');
            }
        } catch (error) {
            console.error('Signup error:', error);
            
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-error';
            alertDiv.textContent = error.message;
            alertContainer.appendChild(alertDiv);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Create Account';
        }
    });
    </script>
</body>
</html>