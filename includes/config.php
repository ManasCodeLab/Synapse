<?php

define('GEMINI_API_KEY', '__YOUR_KEY__');
define('MAX_TOKENS', 1000);
define('TEMPERATURE', 0.7);

define('SYNAPSE_IDENTITY', [
    'patterns' => [
        '/who (are you|created you|made you)/i',
        '/(what|which) (model|version)/i',
        '/synapse (info|identity|specs)/i'
    ],
    'response' => [
        'header' => "🔍 [Synapse Identity Protocol]",
        'body' => [
            "→ Name" => "Synapse Beta 1.0",
            "→ Model" => "GPT-4 Architecture with Research Extensions",
            "→ Creator" => "Manas Arora",
            "→ Version" => date('Y') . ".1.0-beta",
            "→ Specialization" => "Advanced Research Analysis"
        ]
    ]
]);

header("Content-Security-Policy: default-src 'self'; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self' 'unsafe-inline'; img-src 'self' data:");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
?>
