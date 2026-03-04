// Menu admin: delete confirmation, reorder with sessionStorage-based scroll-back.

function deleteMenuItem(menuItemId, menuTitle) {
    if (!confirm('Are you sure you want to delete "' + menuTitle + '"?\n\nThis will also delete any child menu items.\n\nThis action cannot be undone.')) {
        return;
    }
    var form = document.getElementById('delete-form');
    form.action = '/admin/menu/' + menuItemId;
    form.submit();
}

function moveMenuItem(menuItemId, direction) {
    sessionStorage.setItem('scrollToMenuItem', menuItemId);
    document.getElementById('reorder-menu-item-id').value = menuItemId;
    document.getElementById('reorder-direction').value = direction;
    document.getElementById('reorder-form').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    var scrollToMenuItemId = sessionStorage.getItem('scrollToMenuItem');
    if (scrollToMenuItemId) {
        var menuElement = document.getElementById('menu-' + scrollToMenuItemId);
        if (menuElement) {
            setTimeout(function() {
                menuElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                menuElement.style.transition = 'background-color 0.5s';
                menuElement.style.backgroundColor = 'rgba(72, 199, 142, 0.2)';
                setTimeout(function() {
                    menuElement.style.backgroundColor = '';
                }, 1000);
            }, 100);
        }
        sessionStorage.removeItem('scrollToMenuItem');
    }
});
