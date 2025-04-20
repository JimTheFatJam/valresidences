document.addEventListener('DOMContentLoaded', function () {
    setupImagePreview('apartmentImages', 'previewApartmentImages');
    setupImagePreview('unitImages', 'previewUnitImages');
});

function setupImagePreview(inputId, containerId) {
    const imageInput = document.getElementById(inputId);
    const imageContainer = document.getElementById(containerId);

    if (!imageInput || !imageContainer) return;

    // Initially hide the container
    imageContainer.style.display = 'none';

    imageInput.addEventListener('change', function () {
        const files = imageInput.files;

        // If no images selected
        if (files.length === 0) {
            imageContainer.innerHTML = '';
            imageContainer.style.display = 'none';
            return;
        }

        // Show container if images are present
        imageContainer.innerHTML = '';
        imageContainer.style.display = 'flex'; // Or 'block', depending on your layout

        Array.from(files).forEach(file => {
            const reader = new FileReader();

            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Preview';
                imageContainer.appendChild(img);
            };

            reader.readAsDataURL(file);
        });
    });
}
