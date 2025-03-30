
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
    -ms-overflow-style: none;
    scrollbar-width: none;
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
    margin-left: 2rem;
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

