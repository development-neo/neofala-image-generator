<div class="history-section container mx-auto p-6 bg-white rounded-lg shadow-lg mt-8" id="historySection">
    <h2 class="text-xl font-semibold mb-4">Generation History</h2>
    <div class="history-list" id="historyList">
        <!-- History items will be dynamically loaded here -->
        <p class="text-gray-500">No generation history yet.</p>
    </div>
</div>

<!-- History Modal Structure (hidden by default) -->
<div id="historyModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden" aria-labelledby="modalTitle" role="dialog" aria-modal="true">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl p-6">
        <div class="modal-header flex justify-between items-center pb-4 border-b">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Generation Details</h3>
            <button id="closeModalBtn" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
        <div class="modal-body pt-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-1">
                    <strong class="block text-sm font-medium text-gray-700 mb-1">Generated Image:</strong>
                    <img id="modalImage" src="" alt="Generated Image" class="w-full h-auto rounded-md shadow-sm">
                </div>
                <div class="col-span-1">
                    <strong class="block text-sm font-medium text-gray-700 mb-1">Original Product:</strong>
                    <img id="modalOriginal" src="" alt="Original Product" class="w-full h-auto rounded-md shadow-sm">
                </div>
            </div>
            <div class="mt-4">
                <strong class="block text-sm font-medium text-gray-700 mb-1">Prompt:</strong>
                <p id="modalPrompt" class="text-sm text-gray-600 break-words"></p>
            </div>
            <div class="mt-2">
                <strong class="block text-sm font-medium text-gray-700 mb-1">Settings:</strong>
                <p id="modalSettings" class="text-sm text-gray-600"></p>
            </div>
            <div class="mt-2">
                <strong class="block text-sm font-medium text-gray-700 mb-1">Generated At:</strong>
                <p id="modalTimestamp" class="text-sm text-gray-600"></p>
            </div>
        </div>
        <div class="modal-footer flex justify-end pt-6">
            <button id="modalDownloadBtn" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 mr-2">Download</button>
            <button id="modalCloseBtn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">Close</button>
        </div>
    </div>
</div>