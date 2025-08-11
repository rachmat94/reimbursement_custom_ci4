<script>
    let imgZoomLevel = 1;

    function showImageModal(imgUrl) {
        imgZoomLevel = 1; // reset zoom setiap buka modal
        const img = document.getElementById('imagePreview');
        img.src = imgUrl;
        img.style.transform = `scale(${imgZoomLevel})`;

        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }

    function zoomInImage() {
        imgZoomLevel += 0.1;
        document.getElementById('imagePreview').style.transform = `scale(${imgZoomLevel})`;
    }

    function zoomOutImage() {
        if (imgZoomLevel > 0.2) {
            imgZoomLevel -= 0.1;
            document.getElementById('imagePreview').style.transform = `scale(${imgZoomLevel})`;
        }
    }

    function resetZoom() {
        imgZoomLevel = 1;
        document.getElementById('imagePreview').style.transform = `scale(${imgZoomLevel})`;
    }
</script>