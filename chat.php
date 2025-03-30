<?php
require_once __DIR__ . '/includes/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Synapse AI - Research Assistant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="assets/style.css" rel="stylesheet">
    <style>
        :root {
            --deep-space: #0A0A0A;
            --cosmic-purple: #6C4DF6;
            --cyber-teal: #00F5E9;
            --frosted-glass: rgba(255, 255, 255, 0.1);
        }
        
        body {
            background: var(--deep-space);
            color: white;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 2rem;
            min-height: 100vh;
        }

        .glass-panel {
            background: rgba(15, 15, 20, 0.5);
            backdrop-filter: blur(12px);
            border: 1px solid var(--frosted-glass);
            border-radius: 16px;
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .file-upload-wrapper {
            margin: 2rem 0;
        }

        .file-upload-label {
            display: block;
            padding: 2rem;
            background: rgba(108, 77, 246, 0.1);
            border: 2px dashed var(--cosmic-purple);
            border-radius: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            background: rgba(108, 77, 246, 0.2);
        }

        .file-upload-input {
            display: none;
        }

        .file-info {
            display: block;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .search-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .search-bar {
            flex: 1;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--frosted-glass);
            color: white;
            border-radius: 50px;
            font-size: 1rem;
        }

        .search-bar:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--cosmic-purple);
        }

        .btn {
            background: var(--cosmic-purple);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            background: var(--cyber-teal);
            transform: translateY(-2px);
        }

        .ai-response {
            min-height: 200px;
            padding: 2rem;
            margin-top: 2rem;
        }

        .status-indicator {
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
    </style>
</head>
<body>
<?php include 'assets/header.php'; ?>
    <div class="glass-panel">
        <header>
            <h1>Synapse AI</h1>
            <p class="subtitle">AI-Powered Research Assistant</p>
            <div id="apiStatus" class="status-indicator">🟢 Online</div>
        </header>

        <div class="file-upload-wrapper">
            <label for="fileUpload" id="fileLabel" class="file-upload-label">
                <span class="upload-icon">📁</span>
                <span>Upload Research Document (PDF, DOCX, TXT)</span>
                <span id="fileName" class="file-info">No file selected</span>
            </label>
            <input type="file" id="fileUpload" class="file-upload-input" accept=".pdf,.docx,.txt">
        </div>

        <div class="search-container">
            <input type="text" id="aiPrompt" class="search-bar" placeholder="Ask Synapse anything..." autocomplete="off">
            <button id="askAI" class="btn">
                <span class="btn-icon">🔍</span>
                <span class="btn-text">Ask</span>
            </button>
        </div>

        <div id="aiResponse" class="glass-panel ai-response">
            <p>Your AI-generated responses will appear here...</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const askButton = document.getElementById('askAI');
            const promptInput = document.getElementById('aiPrompt');
            const fileUpload = document.getElementById('fileUpload');
            const responseContainer = document.getElementById('aiResponse');

            async function handleQuery() {
                const prompt = promptInput.value.trim();
                const file = fileUpload.files[0];
                
                if (!prompt && !file) {
                    alert('Please enter a question or upload a file');
                    return;
                }

                responseContainer.innerHTML = '<p class="loading">Processing your request...</p>';

                try {
                    const formData = new FormData();
                    if (prompt) formData.append('prompt', prompt);
                    if (file) formData.append('file', file);

                    const response = await fetch('ai-handler.php', {
                        method: 'POST',
                        body: formData
                    });

                    // First check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        throw new Error(`Server returned unexpected format: ${text.substring(0, 100)}`);
                    }

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || `Request failed with status ${response.status}`);
                    }

                    responseContainer.innerHTML = `<div class="response">${data.response.replace(/\n/g, '<br>')}</div>`;
                } catch (error) {
                    responseContainer.innerHTML = `<div class="error">Error: ${error.message}</div>`;
                    console.error('API Error:', error);
                }
            }

            askButton.addEventListener('click', handleQuery);
            promptInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') handleQuery();
            });
        });
    </script>
</body>
</html>