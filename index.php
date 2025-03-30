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
    <title>Synapse AI - Intelligent Research Assistant</title>
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

.hero {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: radial-gradient(circle at top right, var(--accent-primary) 0%, transparent 60%);
}

.hero-content {
    max-width: 1200px;
    text-align: center;
}

.hero h1 {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    background: linear-gradient(to right, var(--text-primary), var(--accent-secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.search-container {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border-radius: 1rem;
    padding: 2rem;
    margin-top: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.auth-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 2rem;
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

.btn-secondary {
    background: var(--glass-bg);
    color: var(--text-primary);
    border: 1px solid var(--accent-primary);
}

.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    padding: 4rem 2rem;
}

.feature-card {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.login-container,
.signup-container {
    max-width: 90%;
    margin-left: 2rem;
    padding-right: 2rem;
    overflow-x: hidden;
}

@media (max-width: 768px) {
    .hero h1 {
        font-size: 2.5rem;
    }
    
    .auth-buttons {
        flex-direction: column;
    }
    
    .nav {
        padding: 1rem;
    }
    
    .nav-auth {
        gap: 1rem;
        margin-right: 0;
    }
}
a{
    text-decoration: none;
    color: white;
}
    </style>
</head>
<body>
<?php include 'assets/header.php'; ?>
    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Elevate Your Research with AI</h1>
                <p>Harness the power of artificial intelligence to accelerate your research and discovery process</p>
                
                <div class="search-container">
                   <a href="chat.php"><button class="btn btn-primary">AI-Powered Research Assistant</button></a>
                </div>
            </div>
        </section>

        <section class="features">
        <a href="research.php">
            <div class="feature-card">
                <h3>Smart Research</h3>
                <p>AI-powered analysis of complex research papers and documents</p>
            </div>
            <a href="chat.php">
            <div class="feature-card">
                <h3>Chat Assistant</h3>
                <p>Interactive AI chat for real-time research guidance</p>
            </div>
            </a>
            <a href="profile.php">
            <div class="feature-card">
                <h3>Custom Profiles</h3>
                <p>Personalized research experience tailored to your needs</p>
            </div>
            </a>
        </section>
    </main>

    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add loading animation for search
        const searchBtn = document.querySelector('.search-container .btn');
        searchBtn.addEventListener('click', () => {
            searchBtn.classList.add('loading');
            setTimeout(() => {
                searchBtn.classList.remove('loading');
            }, 2000);
        });
    </script>
</body>
</html>
