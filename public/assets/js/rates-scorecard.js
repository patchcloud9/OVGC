// Rates page: open scorecard image in modal on thumbnail click.
document.addEventListener('DOMContentLoaded', function() {
    var thumb = document.getElementById('scorecard-thumb');
    var modal = document.getElementById('scorecard-modal');
    var closeEls = modal.querySelectorAll('.modal-close, .modal-background');
    if (thumb) {
        thumb.addEventListener('click', function(e) {
            e.preventDefault();
            modal.classList.add('is-active');
        });
    }
    closeEls.forEach(function(el) {
        el.addEventListener('click', function() {
            modal.classList.remove('is-active');
        });
    });
});
