<div class="preview-section container mx-auto p-6 bg-white rounded-lg shadow-lg mt-8" id="previewSection" style="display: none;">
    <h2 class="text-xl font-semibold mb-4">Generated Image Preview</h2>
    <div class="preview-image mb-4 text-center">
        <img id="generatedImage" src="" alt="Generated product image" class="max-w-full h-auto rounded-md shadow-md mx-auto">
    </div>
    <div class="preview-details grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div class="detail-item">
            <strong class="block text-sm font-medium text-gray-700">Prompt:</strong>
            <p id="finalPrompt" class="text-sm text-gray-600 break-words"></p>
        </div>
        <div class="detail-item">
            <strong class="block text-sm font-medium text-gray-700">Settings:</strong>
            <p id="generationSettings" class="text-sm text-gray-600"></p>
        </div>
    </div>
    <div class="preview-actions text-center">
        <button id="downloadBtn" class="px-6 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 mr-2">Download</button>
        <button id="regenerateBtn" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">Regenerate</button>
    </div>
</div>