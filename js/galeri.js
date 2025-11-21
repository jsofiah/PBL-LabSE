function previewImage(element) {
    const src = element.querySelector('img').src;
    document.getElementById('modalImage').src = src;
    var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
    myModal.show();
}