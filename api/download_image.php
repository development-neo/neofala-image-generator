<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate CSRF token
if (!isset($input['csrf_token']) || !validateCSRFToken($input['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// Validate image URL
$imageUrl = $input['imageUrl'] ?? '';
if (empty($imageUrl) || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid image URL']);
    exit;
}

// Download the image
$context = stream_context_create([
    'http' => [
        'timeout' => 30,
        'user_agent' => 'Neofala Image Generator/1.0'
    ]
]);

$imageData = file_get_contents($imageUrl, false, $context);

if ($imageData === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to download image']);
    exit;
}

// Determine content type
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->buffer($imageData);

// Set appropriate headers for download
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="generated-image.png"');
header('Content-Length: ' . strlen($imageData));

// Output the image data
echo $imageData;
?>