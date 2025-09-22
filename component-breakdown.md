# Component Breakdown - Neofala Image Generator

## Frontend Components

### 1. Main Application Structure
```
index.php
├── Header (navigation, branding)
├── Main Content
│   ├── Upload Section
│   │   ├── Product Image Upload
│   │   └── Style Reference Upload (optional)
│   ├── Creative Controls Section
│   │   ├── Aspect Ratio Selector
│   │   ├── Lighting Style Dropdown
│   │   ├── Camera Perspective Dropdown
│   │   └── Additional Prompt Textarea
│   ├── Action Buttons
│   │   ├── Generate Button
│   │   └── Clear Button
│   ├── Preview Section
│   │   ├── Generated Image Display
│   │   └── Download Button
│   └── History Section
│       ├── Previous Generations List
│       └── History Details Modal
└── Footer
```

### 2. Upload Component
**File**: `templates/components/upload-area.php`
```php
<div class="upload-area" id="productUpload">
    <div class="upload-zone">
        <input type="file" id="productFile" accept="image/*" hidden>
        <div class="upload-content">
            <svg>...</svg>
            <p>Drag & drop product image here</p>
            <button>Choose File</button>
        </div>
    </div>
    <div class="upload-preview" id="productPreview"></div>
</div>
```

**Features**:
- Drag and drop functionality
- File picker fallback
- Image preview
- File validation (size, type)
- Progress indicator

### 3. Creative Controls Component
**File**: `templates/components/creative-controls.php`
```php
<div class="creative-controls">
    <!-- Aspect Ratio -->
    <div class="control-group">
        <label>Aspect Ratio</label>
        <select id="aspectRatio">
            <option value="1:1">1:1 (Square)</option>
            <option value="3:4">3:4 (Portrait)</option>
            <option value="16:9">16:9 (Landscape)</option>
            <option value="custom">Custom</option>
        </select>
    </div>

    <!-- Lighting Style -->
    <div class="control-group">
        <label>Lighting Style</label>
        <select id="lightingStyle">
            <option value="bright-studio">Bright Studio</option>
            <option value="moody-cinematic">Moody & Cinematic</option>
            <option value="natural-light">Natural Light</option>
        </select>
    </div>

    <!-- Camera Perspective -->
    <div class="control-group">
        <label>Camera Perspective</label>
        <select id="cameraPerspective">
            <option value="heroic-low">Heroic Low-Angle</option>
            <option value="eye-level">Eye-Level</option>
            <option value="top-down">Top-Down</option>
        </select>
    </div>

    <!-- Additional Prompt -->
    <div class="control-group">
        <label>Additional Instructions</label>
        <textarea id="additionalPrompt" placeholder="Add custom instructions..."></textarea>
    </div>
</div>
```

### 4. Preview Component
**File**: `templates/components/preview.php`
```php
<div class="preview-section" id="previewSection" style="display: none;">
    <div class="preview-header">
        <h3>Generated Image</h3>
        <div class="preview-actions">
            <button id="downloadBtn">Download</button>
            <button id="regenerateBtn">Regenerate</button>
        </div>
    </div>
    <div class="preview-image">
        <img id="generatedImage" src="" alt="Generated product image">
    </div>
    <div class="preview-details">
        <div class="detail-item">
            <strong>Prompt:</strong>
            <p id="finalPrompt"></p>
        </div>
        <div class="detail-item">
            <strong>Settings:</strong>
            <p id="generationSettings"></p>
        </div>
    </div>
</div>
```

### 5. History Component
**File**: `templates/components/history.php`
```php
<div class="history-section">
    <h3>Generation History</h3>
    <div class="history-list" id="historyList">
        <!-- History items will be dynamically added here -->
    </div>
</div>

<!-- History Modal -->
<div class="history-modal" id="historyModal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Generation Details</h4>
            <button class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <img id="modalImage" src="" alt="Generated image">
            <div class="modal-details">
                <div class="detail-row">
                    <strong>Original Image:</strong>
                    <img id="modalOriginal" src="" alt="Original product">
                </div>
                <div class="detail-row">
                    <strong>Style Reference:</strong>
                    <img id="modalStyle" src="" alt="Style reference">
                </div>
                <div class="detail-row">
                    <strong>Prompt:</strong>
                    <p id="modalPrompt"></p>
                </div>
                <div class="detail-row">
                    <strong>Settings:</strong>
                    <p id="modalSettings"></p>
                </div>
                <div class="detail-row">
                    <strong>Generated:</strong>
                    <p id="modalTimestamp"></p>
                </div>
            </div>
        </div>
    </div>
</div>
```

## Backend Components

### 1. Configuration File
**File**: `includes/config.php`
```php
<?php
// Database configuration (if needed)
define('DB_HOST', 'localhost');
define('DB_USER', 'username');
define('DB_PASS', 'password');
define('DB_NAME', 'neofala_image_generator');

// API Configuration
define('OPENROUTER_API_KEY', 'your_api_key_here');
define('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/images/generations');

// File Upload Configuration
define('UPLOAD_MAX_SIZE', 10485760); // 10MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/jpg', 'image/png']);
define('UPLOAD_DIR', __DIR__ . '/../uploads/temp/');
define('PROCESSED_DIR', __DIR__ . '/../uploads/processed/');

// Session Configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('HISTORY_LIMIT', 10); // Max items in history

// Rate Limiting
define('RATE_LIMIT_REQUESTS', 5);
define('RATE_LIMIT_WINDOW', 60); // seconds
?>
```

### 2. File Upload Handler
**File**: `includes/functions.php`
```php
function handleFileUpload($file, $type = 'product') {
    // Validate file
    if (!validateFile($file)) {
        return ['success' => false, 'message' => 'Invalid file'];
    }

    // Generate unique filename
    $filename = generateUniqueFilename($file['name'], $type);
    $filepath = UPLOAD_DIR . $filename;

    // Move file to temp directory
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'fileinfo' => getImageInfo($filepath)
        ];
    }

    return ['success' => false, 'message' => 'File upload failed'];
}

function validateFile($file) {
    // Check file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return false;
    }

    // Check file type
    $allowedTypes = UPLOAD_ALLOWED_TYPES;
    $fileType = mime_content_type($file['tmp_name']);
    
    return in_array($fileType, $allowedTypes);
}
```

### 3. Prompt Generator
**File**: `includes/prompt-generator.php`
```php
function generateFinalPrompt($productImage, $styleImage, $controls, $additionalPrompt) {
    $prompt = "Task: Generate a new image based on the provided product photo.\n";
    $prompt .= "Primary Subject: The object in the first uploaded image.\n";
    
    // Add creative controls
    if (!empty($controls['aspect_ratio'])) {
        $prompt .= "Creative Direction: Aspect Ratio: {$controls['aspect_ratio']}.\n";
    }
    
    if (!empty($controls['lighting_style'])) {
        $prompt .= "Lighting Style: {$controls['lighting_style']}.\n";
    }
    
    if (!empty($controls['camera_perspective'])) {
        $prompt .= "Camera Perspective: {$controls['camera_perspective']}.\n";
    }
    
    // Add additional instructions
    if (!empty($additionalPrompt)) {
        $prompt .= "Additional Instructions: {$additionalPrompt}.\n";
    }
    
    // Add style reference if provided
    if (!empty($styleImage)) {
        $prompt .= "Take strong inspiration from the provided style reference image, matching its overall aesthetic, color palette, mood, and texture.\n";
    }
    
    $prompt .= "Output a single, high-resolution image without any text, watermarks, or logos.";
    
    return $prompt;
}
```

### 4. AI Integration
**File**: `api/generate.php`
```php
<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$productImage = $_POST['productImage'] ?? '';
$styleImage = $_POST['styleImage'] ?? '';
$controls = $_POST['controls'] ?? [];
$additionalPrompt = $_POST['additionalPrompt'] ?? '';

// Generate final prompt
$finalPrompt = generateFinalPrompt($productImage, $styleImage, $controls, $additionalPrompt);

// Call OpenRouter API
$result = callOpenRouterAPI($finalPrompt);

echo json_encode($result);
?>
```

### 5. Session Management
**File**: `includes/session-manager.php`
```php
class SessionManager {
    private static $instance = null;
    
    private function __construct() {
        session_start();
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
    }
    
    public function getHistory() {
        return $_SESSION['generation_history'] ?? [];
    }
}
```

## JavaScript Components

### 1. Main Application Logic
**File**: `assets/js/app.js`
```javascript
class NeofalaImageGenerator {
    constructor() {
        this.initializeElements();
        this.bindEvents();
        this.initializeDragAndDrop();
    }
    
    initializeElements() {
        this.productUpload = document.getElementById('productUpload');
        this.styleUpload = document.getElementById('styleUpload');
        this.generateBtn = document.getElementById('generateBtn');
        this.previewSection = document.getElementById('previewSection');
        this.historyList = document.getElementById('historyList');
    }
    
    bindEvents() {
        this.generateBtn.addEventListener('click', () => this.generateImage());
        // Other event bindings...
    }
    
    initializeDragAndDrop() {
        // Drag and drop functionality implementation
    }
    
    async generateImage() {
        const formData = new FormData();
        // Collect form data and send to API
        const response = await fetch('/api/generate.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        this.displayResult(result);
    }
    
    displayResult(result) {
        // Display generated image and update history
    }
}
```

## CSS Components

### 1. TailwindCSS Configuration
**File**: `assets/css/tailwind.css`
```css
/* Custom TailwindCSS styles for the application */
.upload-zone {
    @apply border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors;
}

.upload-preview {
    @apply mt-4 rounded-lg overflow-hidden;
}

.creative-controls {
    @apply grid grid-cols-1 md:grid-cols-2 gap-6;
}

.control-group {
    @apply space-y-2;
}

.preview-section {
    @apply mt-8 p-6 bg-white rounded-lg shadow-lg;
}

.history-item {
    @apply flex items-center space-x-4 p-4 border rounded-lg hover:bg-gray-50;
}