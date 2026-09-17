// Admin homepage settings: image clear confirms, file name display, color picker sync.

function clearHeroImage() {
    if (confirm('Are you sure you want to remove this image?')) {
        document.getElementById('clearHeroImageForm').submit();
    }
}

function clearBottomImage() {
    if (confirm('Are you sure you want to remove this image?')) {
        document.getElementById('clearBottomImageForm').submit();
    }
}

function clearCameraImage() {
    if (confirm('Are you sure you want to remove the camera maintenance image?')) {
        document.getElementById('clearCameraImageForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Update file input display names
    var fileInputs = document.querySelectorAll('.file-input');
    fileInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            var fileName = (e.target.files[0] && e.target.files[0].name) || 'No file chosen';
            var fileNameSpan = input.parentElement.querySelector('.file-name');
            if (fileNameSpan) {
                fileNameSpan.textContent = fileName;
            }
        });
    });

    // Sync color picker with hex text input
    var colorInputs = document.querySelectorAll('.color-preview');
    colorInputs.forEach(function(colorInput) {
        var textInput = colorInput.parentElement.nextElementSibling.querySelector('input[type="text"]');
        colorInput.addEventListener('input', function() {
            if (textInput) textInput.value = this.value.toUpperCase();
        });
        if (textInput) {
            textInput.addEventListener('input', function() {
                var value = this.value.trim();
                if (/^#[0-9A-F]{6}$/i.test(value)) {
                    colorInput.value = value;
                }
            });
        }
    });
});
