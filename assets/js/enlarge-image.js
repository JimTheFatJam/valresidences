function openModal(image) {
    const modal = document.getElementById("imageModal");
    const modalImage = document.getElementById("modalImage");
    modal.style.display = "flex";
    modalImage.src = image.src;
}

// Function to close the modal
function closeModal() {
    const modal = document.getElementById("imageModal");
    modal.style.display = "none";
}