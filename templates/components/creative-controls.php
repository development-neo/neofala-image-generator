<div class="creative-controls container mx-auto p-6 bg-white rounded-lg shadow-lg mt-8">
    <h2 class="text-xl font-semibold mb-4">Creative Controls</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Aspect Ratio -->
        <div class="control-group">
            <label for="aspectRatio" class="block text-sm font-medium text-gray-700 mb-1">Aspect Ratio</label>
            <select id="aspectRatio" name="aspectRatio" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="1:1">1:1 (Square)</option>
                <option value="3:4">3:4 (Portrait)</option>
                <option value="16:9">16:9 (Landscape)</option>
                <option value="custom">Custom</option>
            </select>
        </div>

        <!-- Lighting Style -->
        <div class="control-group">
            <label for="lightingStyle" class="block text-sm font-medium text-gray-700 mb-1">Lighting Style</label>
            <select id="lightingStyle" name="lightingStyle" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="bright-studio">Bright Studio</option>
                <option value="moody-cinematic">Moody & Cinematic</option>
                <option value="natural-light">Natural Light</option>
            </select>
        </div>

        <!-- Camera Perspective -->
        <div class="control-group">
            <label for="cameraPerspective" class="block text-sm font-medium text-gray-700 mb-1">Camera Perspective</label>
            <select id="cameraPerspective" name="cameraPerspective" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="heroic-low-angle">Heroic Low-Angle</option>
                <option value="eye-level">Eye-Level</option>
                <option value="top-down">Top-Down</option>
            </select>
        </div>

        <!-- Additional Prompt -->
        <div class="control-group md:col-span-2">
            <label for="additionalPrompt" class="block text-sm font-medium text-gray-700 mb-1">Additional Instructions</label>
            <textarea id="additionalPrompt" name="additionalPrompt" rows="3" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Add custom instructions, e.g., 'Add a bucket of real oranges to the background.'"></textarea>
        </div>
    </div>
</div>