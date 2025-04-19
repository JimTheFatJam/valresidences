document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('apartmentImages');
    const imageContainer = document.getElementById('previewApartmentImages');

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
                imageContainer.appendChild(img);
            };

            reader.readAsDataURL(file);
        });
    });
});  