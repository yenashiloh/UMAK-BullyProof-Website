class ImageGallery {
    constructor() {
        this.currentImageIndex = 0;
        this.images = window.galleryImages || [];
        this.viewer = document.getElementById('galleryViewer');
        this.fullImage = document.getElementById('fullImage');
        
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Initialize click events for thumbnails
        document.querySelectorAll('.gallery-image').forEach(image => {
            image.addEventListener('click', (e) => {
                const index = parseInt(e.target.dataset.galleryIndex);
                this.openGallery(index);
            });
        });

        // Navigation buttons
        document.getElementById('prevImage').addEventListener('click', () => {
            this.navigateImage(-1);
        });

        document.getElementById('nextImage').addEventListener('click', () => {
            this.navigateImage(1);
        });

        // Close button
        document.getElementById('closeGallery').addEventListener('click', () => {
            this.closeGallery();
        });

        // Close on click outside
        this.viewer.addEventListener('click', (e) => {
            if (e.target === this.viewer) {
                this.closeGallery();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (this.viewer.classList.contains('show')) {
                switch(e.key) {
                    case 'ArrowLeft':
                        this.navigateImage(-1);
                        break;
                    case 'ArrowRight':
                        this.navigateImage(1);
                        break;
                    case 'Escape':
                        this.closeGallery();
                        break;
                }
            }
        });
    }

    openGallery(index) {
        this.currentImageIndex = index;
        this.viewer.classList.remove('d-none');
        setTimeout(() => this.viewer.classList.add('show'), 10);
        this.updateImage();
    }

    closeGallery() {
        this.viewer.classList.remove('show');
        setTimeout(() => this.viewer.classList.add('d-none'), 300);
    }

    navigateImage(direction) {
        this.currentImageIndex = (this.currentImageIndex + direction + this.images.length) % this.images.length;
        this.updateImage();
    }

    updateImage() {
        this.fullImage.src = `data:image/jpeg;base64,${this.images[this.currentImageIndex]}`;
    }
}

// Initialize the gallery when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ImageGallery();
});