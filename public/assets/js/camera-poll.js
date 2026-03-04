// Camera live feed: polls /camera/live every 17 s; retries in 3 s on bad/failed frame.
(function () {
    var visible = document.getElementById('camera1');
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
                visible.src = loader.src;
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
        loader.src = '/camera/live?t=' + Date.now();
    }
    // Load first frame immediately, then poll every 17 seconds (3s on failure)
    loadFrame();
})();
