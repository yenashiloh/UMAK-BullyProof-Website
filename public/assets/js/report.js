class ImageGallery {
    constructor() {
        this.currentImageIndex = 0;
        this.images = window.galleryImages || [];
        this.viewer = document.getElementById('galleryViewer');
        this.fullImage = document.getElementById('fullImage');
        this.prevButton = document.getElementById('prevImage');
        this.nextButton = document.getElementById('nextImage');
        this.closeButton = document.getElementById('closeGallery');
        
        this.initializeEventListeners();
        this.updateNavigationButtons();
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
        this.prevButton.addEventListener('click', () => {
            this.navigateImage(-1);
        });

        this.nextButton.addEventListener('click', () => {
            this.navigateImage(1);
        });

        // Close button
        this.closeButton.addEventListener('click', () => {
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
            if (this.viewer.classList.contains('show') && this.images.length > 1) {
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

    updateNavigationButtons() {
        const shouldShowNavigation = this.images.length > 1;
        this.prevButton.style.display = shouldShowNavigation ? 'block' : 'none';
        this.nextButton.style.display = shouldShowNavigation ? 'block' : 'none';
    }

    openGallery(index) {
        this.currentImageIndex = index;
        this.viewer.classList.remove('d-none');
        setTimeout(() => this.viewer.classList.add('show'), 10);
        this.updateImage();
        this.updateNavigationButtons();
    }

    closeGallery() {
        // Ensure the close button works even if transitions are involved
        this.viewer.classList.remove('show');
        setTimeout(() => {
            this.viewer.classList.add('d-none');
        }, 300); // Ensure this matches the fade-out duration
    }

    navigateImage(direction) {
        if (this.images.length > 1) {
            this.currentImageIndex = (this.currentImageIndex + direction + this.images.length) % this.images.length;
            this.updateImage();
        }
    }

    updateImage() {
        this.fullImage.src = `data:image/jpeg;base64,${this.images[this.currentImageIndex]}`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ImageGallery();
});
