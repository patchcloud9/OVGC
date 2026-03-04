// Banner create/edit: normalize page path input (leading slash, no double-slashes/hashes).
document.addEventListener('DOMContentLoaded', function() {
    var pageInput = document.getElementById('banner-page-input');
    var clearBtn = document.getElementById('clear-page');
    function toggleClear() {
        clearBtn.style.display = pageInput.value.length ? 'block' : 'none';
    }
    function normalize() {
        var v = pageInput.value;
        v = v.replace(/\/+/g, '/').replace(/#/g, '');
        if (!v.startsWith('/')) v = '/' + v;
        if (v.length > 1 && v.endsWith('/')) v = v.slice(0, -1);
        pageInput.value = v;
    }
    pageInput.addEventListener('input', function() {
        normalize();
        toggleClear();
    });
    pageInput.addEventListener('blur', normalize);
    pageInput.closest('form').addEventListener('submit', normalize);
    clearBtn.addEventListener('click', function() {
        pageInput.value = '';
        toggleClear();
        pageInput.focus();
    });
    toggleClear();
});
