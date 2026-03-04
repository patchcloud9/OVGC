(function () {
    'use strict';

    var bar     = document.getElementById('dbg-bar');
    var toggle  = document.getElementById('dbg-toggle');
    var panel   = document.getElementById('dbg-panel');

    if (!bar || !toggle || !panel) return;

    // ---------- Toggle open / close ----------

    function open() {
        panel.hidden = false;
        bar.classList.remove('dbg-collapsed');
        toggle.setAttribute('aria-expanded', 'true');
    }

    function close() {
        panel.hidden = true;
        bar.classList.add('dbg-collapsed');
        toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', function () {
        if (panel.hidden) { open(); } else { close(); }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !panel.hidden) { close(); }
    });

    // ---------- Tab switching ----------

    var tabs  = bar.querySelectorAll('.dbg-tab');
    var panes = bar.querySelectorAll('.dbg-pane');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var targetId = tab.getAttribute('data-panel');

            tabs.forEach(function (t) {
                t.classList.remove('dbg-tab-active');
                t.setAttribute('aria-selected', 'false');
            });
            panes.forEach(function (p) {
                p.hidden = true;
                p.classList.remove('dbg-pane-active');
            });

            tab.classList.add('dbg-tab-active');
            tab.setAttribute('aria-selected', 'true');

            var target = document.getElementById(targetId);
            if (target) {
                target.hidden = false;
                target.classList.add('dbg-pane-active');
            }
        });
    });

    // ---------- Keep page content above the bar ----------
    // Add bottom padding to <body> so the bar doesn't overlap content.

    function adjustBodyPadding() {
        var h = bar.offsetHeight;
        document.body.style.paddingBottom = h + 'px';
    }

    adjustBodyPadding();
    window.addEventListener('resize', adjustBodyPadding);

    // Re-measure after panel opens/closes (height changes)
    toggle.addEventListener('click', function () {
        // Small delay so the DOM has updated
        setTimeout(adjustBodyPadding, 10);
    });

}());
