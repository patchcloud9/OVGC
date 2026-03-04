// Gallery lightbox overlay: open on thumbnail click, close via button/backdrop/Escape.
document.addEventListener('DOMContentLoaded', function() {
    var opens = document.querySelectorAll('.gallery-open');
    var overlay = document.getElementById('galleryOverlay');
    var backdrop = document.getElementById('galleryOverlayBackdrop');
    var closeBtn = document.getElementById('galleryOverlayClose');
    var imgEl = document.getElementById('galleryOverlayImage');

    function open(data) {
        imgEl.src = data.path || '';
        imgEl.alt = data.title || '';
        overlay.classList.add('is-active');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        closeBtn.focus();
    }

    function close() {
        overlay.classList.remove('is-active');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        imgEl.src = '';
        imgEl.alt = '';
    }

    opens.forEach(function(a) {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            var ds = this.dataset;
            open({ path: ds.path, title: ds.title });
        });
    });

    closeBtn.addEventListener('click', close);
    backdrop.addEventListener('click', close);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') close();
    });

    // Protect overlay image from right-click / drag
    imgEl.addEventListener('contextmenu', function(e) { e.preventDefault(); });
    imgEl.addEventListener('dragstart', function(e) { e.preventDefault(); });
});
