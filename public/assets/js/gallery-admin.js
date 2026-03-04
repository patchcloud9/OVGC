// Gallery admin: file name display, delete confirmation, reorder with scroll-back.

function updateFileName(input) {
    var fileName = (input.files[0] && input.files[0].name) || 'No file selected';
    document.getElementById('file-name').textContent = fileName;
}

function deleteImage(imageId, imageTitle) {
    if (!confirm('Are you sure you want to delete "' + imageTitle + '"?\n\nThis action cannot be undone.')) {
        return;
    }
    var form = document.getElementById('delete-form');
    form.action = '/admin/gallery/' + imageId;
    form.submit();
}

function moveImage(imageId, direction) {
    sessionStorage.setItem('scrollToImage', imageId);
    document.getElementById('reorder-image-id').value = imageId;
    document.getElementById('reorder-direction').value = direction;
    document.getElementById('reorder-form').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    var scrollToImageId = sessionStorage.getItem('scrollToImage');
    if (scrollToImageId) {
        var imageElement = document.getElementById('image-' + scrollToImageId);
        if (imageElement) {
            setTimeout(function() {
                imageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                imageElement.style.transition = 'background-color 0.5s';
                imageElement.style.backgroundColor = 'rgba(72, 199, 142, 0.1)';
                setTimeout(function() {
                    imageElement.style.backgroundColor = '';
                }, 1000);
            }, 100);
        }
        sessionStorage.removeItem('scrollToImage');
    }
});
