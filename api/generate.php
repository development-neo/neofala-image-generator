<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// --- Session Management Initialization ---
$sessionManager = SessionManager::getInstance();

// --- Basic Security Checks ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// CSRF Token validation
$csrfToken = $_POST['csrf_token'] ?? '';
if (!validateCSRFToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// --- Rate Limiting Check ---
if (!isset($_SESSION['request_count'])) {
    $_SESSION['request_count'] = 0;
    $_SESSION['request_time'] = time();
}

if (time() - $_SESSION['request_time'] > RATE_LIMIT_WINDOW) {
    // Reset count if window has passed
    $_SESSION['request_count'] = 0;
    $_SESSION['request_time'] = time();
}

$_SESSION['request_count']++;

if ($_SESSION['request_count'] > RATE_LIMIT_REQUESTS) {
    http_response_code(429); // Too Many Requests
    echo json_encode(['success' => false, 'message' => 'Rate limit exceeded. Please try again later.']);
    exit;
}

// --- File Upload Handling ---
$productFile = $_FILES['productImage'] ?? null;
$styleFile = $_FILES['styleImage'] ?? null; // For future use

$uploadedProductInfo = null;
if ($productFile && $productFile['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadedProductInfo = handleFileUpload($productFile, 'product');
    if (!$uploadedProductInfo['success']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $uploadedProductInfo['message']]);
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Product image is required.']);
    exit;
}

// --- Get Creative Controls and Additional Prompt ---
$aspectRatio = filter_input(INPUT_POST, 'aspectRatio', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$lightingStyle = filter_input(INPUT_POST, 'lightingStyle', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$cameraPerspective = filter_input(INPUT_POST, 'cameraPerspective', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$additionalPrompt = filter_input(INPUT_POST, 'additionalPrompt', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Validate creative controls to ensure they are from allowed options
$allowedAspectRatios = ['1:1', '3:4', '16:9', 'custom'];
$allowedLightingStyles = ['bright-studio', 'moody-cinematic', 'natural-light'];
$allowedCameraPerspectives = ['heroic-low-angle', 'eye-level', 'top-down'];

$controls = [
    'aspectRatio' => in_array($aspectRatio, $allowedAspectRatios) ? $aspectRatio : '1:1', // Default to 1:1 if invalid
    'lightingStyle' => in_array($lightingStyle, $allowedLightingStyles) ? $lightingStyle : 'bright-studio', // Default to bright-studio if invalid
    'cameraPerspective' => in_array($cameraPerspective, $allowedCameraPerspectives) ? $cameraPerspective : 'eye-level' // Default to eye-level if invalid
];

// Sanitize additional prompt (remove duplicate sanitization)
$additionalPrompt = trim($additionalPrompt);

// --- Prompt Generation ---
$finalPrompt = generateFinalPrompt(
    $uploadedProductInfo['filename'], // Pass the filename
    null, // $styleImageName - currently null
    $controls,
    $additionalPrompt
);

// --- Call OpenRouter AI API ---
// In a real implementation, this would involve making an HTTP request
// using cURL or a library like Guzzle.
// For now, we'll use a mock function.
$apiResult = callOpenRouterAPI($finalPrompt);

// --- Process API Result ---
if ($apiResult['success']) {
    // Store in session history
    $sessionManager->addToHistory([
        'originalImage' => $uploadedProductInfo['filepath'], // Store path for potential modal display
        'generatedImage' => $apiResult['data']['imageUrl'],
        'prompt' => $apiResult['data']['prompt'],
        'settings' => [
            'aspectRatio' => $controls['aspectRatio'],
            'lightingStyle' => $controls['lightingStyle'],
            'cameraPerspective' => $controls['cameraPerspective']
        ]
    ]);

    echo json_encode([
        'success' => true,
        'data' => [
            'imageUrl' => $apiResult['data']['imageUrl'],
            'prompt' => $apiResult['data']['prompt']
        ]
    ]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => $apiResult['message'] ?? 'Failed to generate image from AI.']);
}
?>