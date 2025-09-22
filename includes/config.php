<?php
// Configuration file for Neofala Image Generator

// Ensure session is started if not already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Environment Configuration
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', APP_ENV === 'development');

// Security Configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SECURE_COOKIES', APP_ENV === 'production');

// API Configuration  
define('OPENROUTER_API_KEY', getenv('OPENROUTER_API_KEY') ?: '');
define('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions');

// File Upload Configuration
define('UPLOAD_MAX_SIZE', 10485760); // 10MB
define('UPLOAD_ALLOWED_TYPES', ['image/png', 'image/jpeg', 'image/jpg']);
define('UPLOAD_DIR', __DIR__ . '/../uploads/temp/');
define('PROCESSED_DIR', __DIR__ . '/../uploads/processed/');

// Session Configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('HISTORY_LIMIT', 10); // Max items in history

// Rate Limiting
define('RATE_LIMIT_REQUESTS', 5);
define('RATE_LIMIT_WINDOW', 60); // seconds

// Create upload directories if they don't exist
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
if (!is_dir(PROCESSED_DIR)) {
    mkdir(PROCESSED_DIR, 0755, true);
}

// CSRF Token generation
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// CSRF Token validation
function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}
?>