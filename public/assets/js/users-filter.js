// Users list: client-side search by name/email and filter by role.
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('searchInput');
    var roleFilter = document.getElementById('roleFilter');
    var userCards = document.querySelectorAll('.user-card');
    var noResults = document.getElementById('noResults');

    function filterUsers() {
        var searchTerm = searchInput.value.toLowerCase();
        var roleValue = roleFilter.value.toLowerCase();
        var visibleCount = 0;

        userCards.forEach(function(card) {
            var name = card.dataset.name;
            var email = card.dataset.email;
            var role = card.dataset.role;
            var matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            var matchesRole = roleValue === '' || role === roleValue;
            if (matchesSearch && matchesRole) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', filterUsers);
    roleFilter.addEventListener('change', filterUsers);
});
