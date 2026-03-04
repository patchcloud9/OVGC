// Gallery upload form: toggle price amount and prints URL fields based on selections.
function togglePriceAmount() {
    var priceType = document.getElementById('price-type-select').value;
    var priceAmountField = document.getElementById('price-amount-field');
    priceAmountField.style.display = priceType === 'amount' ? 'block' : 'none';
}

function togglePrintsUrl() {
    var printsCheckbox = document.getElementById('prints-available-checkbox');
    var printsUrlField = document.getElementById('prints-url-field');
    printsUrlField.style.display = printsCheckbox.checked ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    togglePriceAmount();
    togglePrintsUrl();
});
