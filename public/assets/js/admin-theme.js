// Admin theme settings: reset confirm, file name display, color picker ↔ hex text sync.

function confirmReset() {
    return confirm('Are you sure you want to reset all theme settings to defaults? This action cannot be undone.');
}

function updateFileName(input, displayId) {
    var display = document.getElementById(displayId);
    if (input.files && input.files[0]) {
        display.textContent = input.files[0].name;
    } else {
        display.textContent = 'No file selected';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(function(colorInput) {
        var textInput = colorInput.parentElement.nextElementSibling.querySelector('input[type="text"]');
        colorInput.addEventListener('input', function() {
            textInput.value = this.value.toUpperCase();
        });
        textInput.addEventListener('input', function() {
            var value = this.value.trim();
            if (/^#[0-9A-F]{6}$/i.test(value)) {
                colorInput.value = value;
            }
        });
    });
});
