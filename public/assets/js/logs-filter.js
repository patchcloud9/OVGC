// Logs: client-side search by message and filter by level; hides pagination when active.
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('searchInput');
    var levelFilter = document.getElementById('levelFilter');
    var logCards = document.querySelectorAll('.log-card');
    var noResults = document.getElementById('noResults');
    var pagination = document.querySelector('.pagination');
    var summary = document.querySelector('.has-text-centered.has-text-grey');

    function filterLogs() {
        var searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        var levelValue = levelFilter ? levelFilter.value.toLowerCase() : '';
        var visibleCount = 0;

        logCards.forEach(function(card) {
            var message = card.dataset.message || '';
            var level = card.dataset.level || '';
            var matchesSearch = !searchTerm || message.includes(searchTerm);
            var matchesLevel = !levelValue || level === levelValue;
            if (matchesSearch && matchesLevel) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }
        if (pagination) {
            pagination.style.display = (searchTerm || levelValue) ? 'none' : '';
        }
        if (summary) {
            summary.style.display = (searchTerm || levelValue) ? 'none' : '';
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterLogs);
    if (levelFilter) levelFilter.addEventListener('change', filterLogs);
});
