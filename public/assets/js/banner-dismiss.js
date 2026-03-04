// Banner dismiss logic: store dismissed IDs in a cookie so closed banners stay hidden.
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.banner .delete').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var banner = this.closest('.banner');
            if (!banner) return;
            var id = banner.dataset.id;
            if (id) {
                var existing = document.cookie.replace(/(?:(?:^|.*;\s*)dismissed_banners\s*\=\s*([^;]*).*$)|^.*$/, "$1");
                var arr = existing ? existing.split(',') : [];
                if (arr.indexOf(id) === -1) {
                    arr.push(id);
                    document.cookie = 'dismissed_banners=' + arr.join(',') + '; path=/; max-age=' + (30*24*60*60);
                }
            }
            banner.remove();
        });
    });
});
