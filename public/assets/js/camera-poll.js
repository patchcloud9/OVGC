// Camera live feed(s): polls each .js-camera-feed element's endpoint every 17 s;
// retries in 3 s on a bad/failed frame. Supports any number of camera columns —
// each element polls independently using its own data-endpoint.
(function () {
    function attach(img) {
        var endpoint = img.getAttribute('data-endpoint');
        if (!endpoint) return;

        var timer = null;
        function scheduleNext(delay) {
            clearTimeout(timer);
            timer = setTimeout(loadFrame, delay);
        }
        function loadFrame() {
            var loader = new Image();
            loader.onload = function () {
                // naturalWidth === 0 means the browser got a response but
                // couldn't decode the image (e.g. truncated JPEG mid-write).
                if (loader.naturalWidth > 0) {
                    img.src = loader.src;
                    scheduleNext(17000);
                } else {
                    // Bad frame — retry quickly
                    scheduleNext(3000);
                }
            };
            loader.onerror = function () {
                // Network/server error — retry quickly
                scheduleNext(3000);
            };
            loader.src = endpoint + '?t=' + Date.now();
        }
        // Load first frame immediately, then poll every 17 seconds (3s on failure)
        loadFrame();
    }

    var feeds = document.querySelectorAll('.js-camera-feed');
    for (var i = 0; i < feeds.length; i++) {
        attach(feeds[i]);
    }
})();
