<?php
ob_start();
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests allowed', 405);
    }

    $prompt = trim($_POST['prompt'] ?? '');
    $fileContent = null;
    $mimeType = null;

    $identity_questions = [
        '/\b(your name|who (are you|created you|made you)|what(\'?s| is) your (name|model|version)|synapse (version|info|model)\b/i'
    ];

    foreach ($identity_questions as $pattern) {
        if (preg_match($pattern, strtolower($prompt))) {
            echo json_encode([
                'success' => true,
                'response' => "🔍 Synapse Identity Protocol\n\n" .
                             "→ Name: Synapse Beta 1.0\n" .
                             "→ Model: GPT-4 Architecture\n" .
                             "→ Creator: Manas Arora\n" .
                             "→ Version: v1.0.2024\n" .
                             "→ Capabilities: Research Analysis"
            ]);
            exit;
        }
    }

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        $allowedTypes = ['application/pdf', 'text/plain'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Only PDF and text files allowed', 400);
        }
        
        if ($file['size'] > 5000000) {
            throw new Exception('File size too large (max 5MB)', 400);
        }
        
        $fileContent = base64_encode(file_get_contents($file['tmp_name']));
        $mimeType = $file['type'];
    }

    if (empty($prompt) && empty($fileContent)) {
        throw new Exception('Please enter a question or upload a file', 400);
    }

    $model = 'gemini-1.5-flash';
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . GEMINI_API_KEY;

    $payload = [
        'contents' => [
            'parts' => array_filter([
                ['text' => "You are Synapse Beta 1.0. Always respond formally."],
                !empty($prompt) ? ['text' => $prompt] : null,
                !empty($fileContent) ? [
                    'file_data' => [
                        'mime_type' => $mimeType,
                        'data' => $fileContent
                    ]
                ] : null
            ])
        ],
        'generationConfig' => [
            'temperature' => 0.7
        ]
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FAILONERROR => true
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception("API connection failed: " . curl_error($ch), 502);
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!str_contains($response, '{')) {
        throw new Exception("API returned malformed response", 500);
    }

    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Failed to decode API response", 500);
    }

    if ($httpCode !== 200) {
        $errorMsg = $result['error']['message'] ?? 'API request failed';
        throw new Exception($errorMsg, $httpCode);
    }

    ob_end_clean();
    
    echo json_encode([
        'success' => true,
        'response' => $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated'
    ]);

} catch (Exception $e) {
    ob_end_clean();
    
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => "Synapse Error: " . $e->getMessage()
    ]);
}