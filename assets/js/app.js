class NeofalaImageGenerator {
    constructor() {
        this.elements = {
            productUpload: document.getElementById('productUpload'),
            dropZone: document.getElementById('dropZone'),
            productFile: document.getElementById('productFile'),
            chooseFileBtn: document.getElementById('chooseFileBtn'),
            productPreview: document.getElementById('productPreview'),
            generateBtn: document.getElementById('generateBtn'),
            previewSection: document.getElementById('previewSection'),
            generatedImage: document.getElementById('generatedImage'),
            finalPrompt: document.getElementById('finalPrompt'),
            generationSettings: document.getElementById('generationSettings'),
            historyList: document.getElementById('historyList'),
            historySection: document.getElementById('historySection'),
            historyModal: document.getElementById('historyModal'),
            closeModalBtn: document.getElementById('closeModalBtn'),
            modalImage: document.getElementById('modalImage'),
            modalOriginal: document.getElementById('modalOriginal'),
            modalStyle: document.getElementById('modalStyle'),
            modalPrompt: document.getElementById('modalPrompt'),
            modalSettings: document.getElementById('modalSettings'),
            modalTimestamp: document.getElementById('modalTimestamp'),
            modalDownloadBtn: document.getElementById('modalDownloadBtn'),
            modalCloseBtn: document.getElementById('modalCloseBtn'),
            aspectRatio: document.getElementById('aspectRatio'),
            lightingStyle: document.getElementById('lightingStyle'),
            cameraPerspective: document.getElementById('cameraPerspective'),
            additionalPrompt: document.getElementById('additionalPrompt'),
            regenerateBtn: document.getElementById('regenerateBtn'),
            downloadBtn: document.getElementById('downloadBtn')
        };

        this.selectedProductFile = null;
        this.selectedStyleFile = null;
        this.csrfToken = null;

        this.bindEvents();
        this.initializeDragAndDrop();
        this.loadHistory();
        this.getCSRFToken();
    }

    // Get CSRF token from server
    async getCSRFToken() {
        try {
            const response = await fetch('api/csrf_token.php');
            const data = await response.json();
            this.csrfToken = data.token;
        } catch (error) {
            console.error('Failed to get CSRF token:', error);
        }
    }

    bindEvents() {
        // File input change
        this.elements.productFile.addEventListener('change', (e) => this.handleFileSelect(e.target.files[0]));
        // Choose File button click
        this.elements.chooseFileBtn.addEventListener('click', () => this.elements.productFile.click());
        // Generate button click
        this.elements.generateBtn.addEventListener('click', () => this.generateImage());
        // Regenerate button click
        if (this.elements.regenerateBtn) {
            this.elements.regenerateBtn.addEventListener('click', () => this.regenerateImage());
        }
        // Download button click
        if (this.elements.downloadBtn) {
            this.elements.downloadBtn.addEventListener('click', () => this.downloadCurrentImage());
        }
        // Close modal button
        if (this.elements.closeModalBtn) {
            this.elements.closeModalBtn.addEventListener('click', () => this.closeModal());
        }
        if (this.elements.modalCloseBtn) {
            this.elements.modalCloseBtn.addEventListener('click', () => this.closeModal());
        }
        // Modal download button
        if (this.elements.modalDownloadBtn) {
            this.elements.modalDownloadBtn.addEventListener('click', () => this.downloadImage());
        }
    }

    initializeDragAndDrop() {
        const dropZone = this.elements.dropZone;
        const productFile = this.elements.productFile;

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-500');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-500');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.handleFileSelect(files[0]);
            }
        });
    }

    handleFileSelect(file) {
        if (!file) return;

        // Basic file type and size validation (client-side)
        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        const maxSize = 10 * 1024 * 1024; // 10MB

        if (!allowedTypes.includes(file.type)) {
            alert('Invalid file type. Please upload PNG or JPG images.');
            return;
        }
        if (file.size > maxSize) {
            alert('File is too large. Maximum file size is 10MB.');
            return;
        }

        this.selectedProductFile = file;
        this.displayPreview(file);
        this.elements.generateBtn.style.display = 'inline-block'; // Show generate button
    }

    displayPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            this.elements.productPreview.innerHTML = `
                <img src="${e.target.result}" alt="Product Preview" class="max-w-full h-auto rounded-md shadow-md mx-auto">
                <p class="text-sm text-gray-600 mt-2">${file.name} (${(file.size / 1024).toFixed(2)} KB)</p>
            `;
        };
        reader.readAsDataURL(file);
    }

    async generateImage() {
        if (!this.selectedProductFile) {
            alert('Please upload a product image first.');
            return;
        }

        // Disable button and show loading state
        this.elements.generateBtn.disabled = true;
        this.elements.generateBtn.textContent = 'Generating...';
        this.elements.previewSection.style.display = 'block'; // Show preview section

        const formData = new FormData();
        formData.append('productImage', this.selectedProductFile);
        formData.append('styleImage', this.selectedStyleFile || '');
        formData.append('aspectRatio', this.elements.aspectRatio.value);
        formData.append('lightingStyle', this.elements.lightingStyle.value);
        formData.append('cameraPerspective', this.elements.cameraPerspective.value);
        formData.append('additionalPrompt', this.elements.additionalPrompt.value);
        formData.append('csrf_token', this.csrfToken);

        try {
            const response = await fetch('api/generate.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.displayGeneratedImage(result.data.imageUrl, result.data.prompt, {
                    aspectRatio: this.elements.aspectRatio.value,
                    lightingStyle: this.elements.lightingStyle.value,
                    cameraPerspective: this.elements.cameraPerspective.value
                });
                this.saveToHistory({
                    originalImage: URL.createObjectURL(this.selectedProductFile),
                    generatedImage: result.data.imageUrl,
                    prompt: result.data.prompt,
                    timestamp: result.data.createdAt, // Add timestamp from API response
                    settings: {
                        aspectRatio: this.elements.aspectRatio.value,
                        lightingStyle: this.elements.lightingStyle.value,
                        cameraPerspective: this.elements.cameraPerspective.value
                    }
                });
            } else {
                alert('Error generating image: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error during image generation:', error);
            alert('An error occurred while generating the image. Please try again.');
        } finally {
            this.elements.generateBtn.disabled = false;
            this.elements.generateBtn.textContent = 'Generate Image';
        }
    }

    displayGeneratedImage(imageUrl, prompt, settings) {
        this.elements.generatedImage.src = imageUrl;
        this.elements.finalPrompt.textContent = prompt;
        this.elements.generationSettings.textContent = `AR: ${settings.aspectRatio}, Lighting: ${settings.lightingStyle}, Perspective: ${settings.cameraPerspective}`;
        this.elements.previewSection.style.display = 'block';
    }

    regenerateImage() {
        // Re-trigger generation with current settings
        this.generateImage();
    }

    saveToHistory(item) {
        let history = JSON.parse(localStorage.getItem('generationHistory')) || [];
        history.unshift(item); // Add to the beginning
        // Limit history size
        if (history.length > 10) {
            history = history.slice(0, 10);
        }
        localStorage.setItem('generationHistory', JSON.stringify(history));
        this.renderHistory();
    }

    loadHistory() {
        this.renderHistory();
    }

    renderHistory() {
        const history = JSON.parse(localStorage.getItem('generationHistory')) || [];
        const historyList = this.elements.historyList;
        historyList.innerHTML = ''; // Clear existing history

        if (history.length === 0) {
            historyList.innerHTML = '<p class="text-gray-500">No generation history yet.</p>';
            return;
        }

        history.forEach((item, index) => {
            const historyItem = document.createElement('div');
            historyItem.classList.add('history-item', 'cursor-pointer');
            historyItem.dataset.index = index;
            historyItem.innerHTML = `
                <img src="${item.generatedImage}" alt="Generated Thumbnail">
                <div>
                    <p class="font-medium text-sm">${item.prompt.substring(0, 50)}...</p>
                    <p class="text-xs text-gray-500">${item.settings.aspectRatio}, ${item.settings.lightingStyle}</p>
                </div>
            `;
            historyItem.addEventListener('click', () => this.openModal(item, index));
            historyList.appendChild(historyItem);
        });
    }

    openModal(item, index) {
        this.elements.modalImage.src = item.generatedImage;
        this.elements.modalOriginal.src = item.originalImage;
        this.elements.modalStyle.src = item.styleImage || ''; // Handle if style image was used
        this.elements.modalPrompt.textContent = item.prompt;
        this.elements.modalSettings.textContent = `AR: ${item.settings.aspectRatio}, Lighting: ${item.settings.lightingStyle}, Perspective: ${item.settings.cameraPerspective}`;
        // Use the timestamp from the item data if available
        this.elements.modalTimestamp.textContent = item.timestamp ? new Date(item.timestamp).toLocaleString() : new Date().toLocaleString();
        this.elements.historyModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden'); // Prevent background scrolling
    }

    closeModal() {
        this.elements.historyModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    downloadCurrentImage() {
        const imageUrl = this.elements.generatedImage.src;
        this.downloadImageFromUrl(imageUrl, 'generated-image.png');
    }

    downloadImage() {
        const imageUrl = this.elements.modalImage.src;
        this.downloadImageFromUrl(imageUrl, 'generated-image.png');
    }

    async downloadImageFromUrl(imageUrl, filename) {
        if (!imageUrl) return;

        try {
            // For external URLs, we need to proxy through our server
            if (imageUrl.startsWith('http')) {
                const response = await fetch('api/download_image.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        imageUrl: imageUrl,
                        csrf_token: this.csrfToken
                    })
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(downloadUrl);
                } else {
                    throw new Error('Download failed');
                }
            } else {
                // For local URLs, direct download
                const link = document.createElement('a');
                link.href = imageUrl;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        } catch (error) {
            console.error('Download failed:', error);
            alert('Download failed. Please try right-clicking the image and saving it manually.');
        }
    }

    // Placeholder for future style image handling
    // handleStyleFileSelect(file) { ... }
}

document.addEventListener('DOMContentLoaded', () => {
    new NeofalaImageGenerator();
});