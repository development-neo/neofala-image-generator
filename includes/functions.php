<?php
// Include configuration
require_once __DIR__ . '/config.php';

// --- Helper Functions ---

/**
 * Generates a unique filename to prevent overwrites.
 * @param string $originalName The original filename.
 * @param string $prefix A prefix for the filename (e.g., 'product', 'style').
 * @return string The unique filename.
 */
function generateUniqueFilename($originalName, $prefix = '') {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $filename = uniqid($prefix . '_', true) . '.' . $extension;
    return $filename;
}

/**
 * Validates an uploaded file based on type and size.
 * @param array $file The $_FILES['file'] array.
 * @return bool True if the file is valid, false otherwise.
 */
function validateFile($file) {
    // Check if file is uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Check file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return false;
    }

    // Check file type using MIME type
    $allowedTypes = UPLOAD_ALLOWED_TYPES;
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        return false;
    }

    return true;
}

/**
 * Handles the upload of a file, validates it, and moves it to a temporary directory.
 * @param array $file The $_FILES['file'] array.
 * @param string $type A prefix for the filename (e.g., 'product', 'style').
 * @return array An array with upload status and file information.
 */
function handleFileUpload($file, $type = 'product') {
    if (empty($file) || !is_array($file)) {
        return ['success' => false, 'message' => 'No file data provided.'];
    }

    // Validate the file
    if (!validateFile($file)) {
        // Determine specific error message
        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
            $message = 'File is too large.';
        } elseif ($file['error'] === UPLOAD_ERR_PARTIAL) {
            $message = 'File upload was only partially completed.';
        } elseif ($file['error'] === UPLOAD_ERR_NO_FILE) {
            $message = 'No file was uploaded.';
        } else {
            // Check MIME type validation failure
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES)) {
                $message = 'Invalid file type. Please upload PNG or JPG.';
            } else {
                $message = 'File upload failed due to an unknown error.';
            }
        }
        return ['success' => false, 'message' => $message];
    }

    // Ensure upload directory exists
    if (!is_dir(UPLOAD_DIR)) {
        if (!mkdir(UPLOAD_DIR, 0775, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directory.'];
        }
    }

    // Sanitize and generate unique filename
    $originalFilename = $file['name'];
    $sanitizedFilename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalFilename); // Remove invalid characters
    $filename = generateUniqueFilename($sanitizedFilename, $type); // Use sanitized name for uniqueness
    $filepath = UPLOAD_DIR . $filename;

    // Move file to temporary directory
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Get image info for later use (dimensions, etc.)
        $imageInfo = getimagesize($filepath);
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'mimeType' => $mimeType,
            'dimensions' => $imageInfo ? ['width' => $imageInfo[0], 'height' => $imageInfo[1]] : null
        ];
    }

    return ['success' => false, 'message' => 'File upload failed: Could not move uploaded file.'];
}

/**
 * Constructs the final prompt for the AI based on user inputs.
 * @param string $productImageName The filename of the uploaded product image.
 * @param string|null $styleImageName The filename of the uploaded style reference image (or null).
 * @param array $controls An array of creative control settings.
 * @param string $additionalPrompt User's custom prompt text.
 * @return string The final structured prompt.
 */
function generateFinalPrompt($productImageName, $styleImageName = null, $controls = [], $additionalPrompt = '') {
    $prompt = "Task: Generate a new image based on the provided product photo.\n";
    $prompt .= "Primary Subject: The object in the uploaded image '{$productImageName}'.\n";

    // Add creative controls
    if (!empty($controls['aspectRatio'])) {
        $prompt .= "Creative Direction: Aspect Ratio: {$controls['aspectRatio']}.\n";
    }

    if (!empty($controls['lightingStyle'])) {
        $prompt .= "Lighting Style: {$controls['lightingStyle']}.\n";
    }

    if (!empty($controls['cameraPerspective'])) {
        $prompt .= "Camera Perspective: {$controls['cameraPerspective']}.\n";
    }

    // Add additional instructions
    if (!empty($additionalPrompt)) {
        $prompt .= "Additional Instructions: {$additionalPrompt}.\n";
    }

    // Add style reference inspiration if provided
    if (!empty($styleImageName)) {
        $prompt .= "Take strong inspiration from the provided style reference image '{$styleImageName}', matching its overall aesthetic, color palette, mood, and texture.\n";
    }

    $prompt .= "Output a single, high-resolution image without any text, watermarks, or logos.";

    return $prompt;
}

/**
 * Calls the OpenRouter AI API to generate an image.
 * @param string $prompt The prompt to send to the API.
 * @return array An array containing the API response or an error message.
 */
function callOpenRouterAPI($prompt) {
    $apiKey = OPENROUTER_API_KEY;
    $apiUrl = OPENROUTER_API_URL;
    
    // Check if API key is set
    if (empty($apiKey)) {
        return [
            'success' => false,
            'message' => 'OpenRouter API key is not configured. Please set OPENROUTER_API_KEY environment variable.'
        ];
    }

    // OpenRouter API payload for image generation using DALL-E 3
    $data = [
        'model' => 'openai/dall-e-3',
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'max_tokens' => 300,
        'temperature' => 0.7
    ];

    // Initialize cURL session
    $ch = curl_init($apiUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
    curl_setopt($ch, CURLOPT_POST, true); // Set method to POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Set POST data
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
        // OpenRouter specific headers might be needed, e.g., for model selection if not in payload
        // 'X-OpenRouter-Api-Key: ' . $apiKey // Alternative header
    ]);
    // Optional: Disable SSL verification if you encounter issues (not recommended for production)
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // Execute cURL session
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    // Close cURL session
    curl_close($ch);

    // Handle cURL errors
    if ($curlError) {
        return [
            'success' => false,
            'message' => 'cURL Error: ' . $curlError
        ];
    }

    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Check for API errors based on HTTP code or response structure
    if ($httpCode >= 200 && $httpCode < 300) {
        // For testing purposes, return a mock response since OpenRouter image generation
        // requires specific model configuration and actual API key
        // TODO: Replace with actual API response parsing when API key is configured
        
        if (APP_DEBUG && empty(trim($apiKey))) {
            // Return mock response for testing
            return [
                'success' => true,
                'data' => [
                    'imageUrl' => 'https://via.placeholder.com/512x512/FF6B6B/FFFFFF?text=Generated+Image',
                    'prompt' => $prompt,
                    'modelUsed' => 'mock-model',
                    'createdAt' => date('c')
                ]
            ];
        }
        
        // Parse actual API response
        $generatedImageUrl = null;
        $revisedPrompt = $prompt;

        // For OpenRouter chat completions API with image generation models
        if (isset($responseData['choices'][0]['message']['content'])) {
            $content = $responseData['choices'][0]['message']['content'];
            // Extract image URL from response content if present
            if (preg_match('/https?:\/\/[^\s]+\.(jpg|jpeg|png|gif)/i', $content, $matches)) {
                $generatedImageUrl = $matches[0];
            }
        }

        if ($generatedImageUrl) {
            return [
                'success' => true,
                'data' => [
                    'imageUrl' => $generatedImageUrl,
                    'prompt' => $revisedPrompt,
                    'modelUsed' => $responseData['model'] ?? 'unknown',
                    'createdAt' => date('c', $responseData['created'] ?? time())
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'API response did not contain a valid image URL. Response: ' . substr(json_encode($responseData), 0, 200) . '...'
            ];
        }

    } else {
        // Handle API errors
        $errorMessage = 'Unknown API error';
        if (isset($responseData['error']['message'])) {
            $errorMessage = $responseData['error']['message'];
        } elseif (isset($responseData['message'])) {
            $errorMessage = $responseData['message'];
        }
        
        // Log error for debugging
        if (APP_DEBUG) {
            error_log("OpenRouter API Error: " . json_encode($responseData));
        }
        
        return [
            'success' => false,
            'message' => "API Error ({$httpCode}): " . $errorMessage
        ];
    }
}

/**
 * Session Manager Class (as previously defined)
 */
class SessionManager {
    private static $instance = null;

    private function __construct() {
        // Set session cookie parameters for better security
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $cookieParams["lifetime"],
            'path' => $cookieParams["path"],
            'domain' => $cookieParams["domain"],
            'secure' => SECURE_COOKIES, // Use environment-based setting
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // Start the session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addToHistory($data) {
        if (!isset($_SESSION['generation_history'])) {
            $_SESSION['generation_history'] = [];
        }

        $data['timestamp'] = date('Y-m-d H:i:s');
        array_unshift($_SESSION['generation_history'], $data);

        // Keep only recent items
        if (count($_SESSION['generation_history']) > HISTORY_LIMIT) {
            $_SESSION['generation_history'] = array_slice($_SESSION['generation_history'], 0, HISTORY_LIMIT);
        }
        $_SESSION['last_activity'] = time(); // Update last activity time
    }

    public function getHistory() {
        return $_SESSION['generation_history'] ?? [];
    }

    public function isSessionExpired() {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
            return true;
        }
        return false;
    }

    public function destroySession() {
        $_SESSION = array(); // Unset all session variables
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy(); // Destroy session data
    }
}

// --- Utility Functions ---

/**
 * Gets image information (dimensions, etc.) using getimagesize.
 * @param string $filepath The path to the image file.
 * @return array|null An array with image info or null if it fails.
 */
function getImageInfo($filepath) {
    if (!file_exists($filepath)) {
        return null;
    }
    $info = getimagesize($filepath);
    if ($info === false) {
        return null;
    }
    return [
        'mime' => $info['mime'],
        'width' => $info[0],
        'height' => $info[1]
    ];
}

?>