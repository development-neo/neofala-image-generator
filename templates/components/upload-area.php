<div class="upload-area container mx-auto p-6 bg-white rounded-lg shadow-lg" id="productUpload">
    <h2 class="text-xl font-semibold mb-4">Upload Your Product Image</h2>
    <div class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors cursor-pointer" id="dropZone">
        <input type="file" id="productFile" accept="image/png, image/jpeg" class="hidden" />
        <div class="upload-content">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903 3 3 0 111.59-5.421 7 7 0 0111.255 5.755 5 5 0 01-1.01 9.255A5 5 0 017 16z" />
            </svg>
            <p class="mt-2 text-sm text-gray-600">Drag & drop your image here</p>
            <p class="text-xs text-gray-500">(PNG or JPG only)</p>
            <button class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50" id="chooseFileBtn">Choose File</button>
        </div>
    </div>
    <div class="upload-preview mt-4" id="productPreview">
        <!-- Image preview will be shown here -->
    </div>
    <div class="mt-4 text-center">
        <button class="px-6 py-3 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50" id="generateBtn" style="display: none;">Generate Image</button>
    </div>
</div>