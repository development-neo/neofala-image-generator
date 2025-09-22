<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

// Generate and return CSRF token
$token = generateCSRFToken();

echo json_encode([
    'success' => true,
    'token' => $token
]);
?>