setTimeout(function () {
    var alertEl = document.getElementById('addSuccess');
    if (alertEl) {
        var bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
        bsAlert.close();
    }
}, 2500); // 2.5 seconds

const carousel = document.getElementById('propertyCarousel');
carousel.addEventListener('slide.bs.carousel', function (e) {
    // Remove highlight from all thumbnails
    document.querySelectorAll('.img-thumbnail').forEach(img => {
        img.classList.remove('active-thumbnail');
    });
    // Add highlight to the currently active thumbnail
    const nextIndex = e.to;
    const activeThumb = document.getElementById('thumb-' + nextIndex);
    if (activeThumb) {
        activeThumb.classList.add('active-thumbnail');
    }
});