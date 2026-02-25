<!-- hero for Logs -->
<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title is-3"><i class="fas fa-clipboard-list"></i> <?= e($title) ?></h1>
            <p class="subtitle is-6 has-text-white">Application log entries</p>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 1.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <nav class="breadcrumb" aria-label="breadcrumbs">
            <ul>
                <li><a href="/admin">Admin</a></li>
                <li class="is-active"><a href="#" aria-current="page">Logs</a></li>
            </ul>
        </nav>
        
        
        
        <!-- Search and Filter Bar -->
        <div class="box">
            <div class="columns is-multiline">
                <div class="column is-two-thirds-tablet is-three-quarters-desktop">
                    <div class="field">
                        <div class="control has-icons-left">
                            <input class="input" type="text" id="searchInput" placeholder="Search by message...">
                            <span class="icon is-left">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="column is-one-third-tablet is-one-quarter-desktop">
                    <div class="field">
                        <div class="control has-icons-left">
                            <div class="select is-fullwidth">
                                <select id="levelFilter">
                                    <option value="">All Levels</option>
                                    <option value="info">Info</option>
                                    <option value="warning">Warning</option>
                                    <option value="error">Error</option>
                                    <option value="debug">Debug</option>
                                </select>
                            </div>
                            <span class="icon is-left">
                                <i class="fas fa-filter"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Data Source Indicator -->
        <div class="notification <?= $source === 'database' ? 'is-success' : 'is-warning' ?> is-light">
            <div class="level is-mobile">
                <div class="level-left">
                    <div class="level-item">
                        <span class="icon">
                            <i class="fas <?= $source === 'database' ? 'fa-database' : 'fa-file' ?>"></i>
                        </span>
                        <span>
                            <strong>Source:</strong> 
                            <?= $source === 'database' ? 'Database' : 'File Backup' ?>
                        </span>
                    </div>
                </div>
                <div class="level-right">
                    <div class="level-item">
                        <span class="tag <?= $databaseAvailable ? 'is-success' : 'is-danger' ?>">
                            <?= $databaseAvailable ? 'Online' : 'Offline' ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <?php if ($needsSync ?? false): ?>
                <p class="mt-2">
                    <span class="icon-text">
                        <span class="icon has-text-warning"><i class="fas fa-exclamation-triangle"></i></span>
                        <span><?= $fileLogCount ?> log(s) need syncing to database.</span>
                    </span>
                </p>
            <?php endif; ?>
        </div>
        
        <?php if (empty($logs)): ?>
            <div class="notification is-info is-light">
                <span class="icon-text">
                    <span class="icon"><i class="fas fa-info-circle"></i></span>
                    <span>No log entries found.</span>
                </span>
            </div>
        <?php else: ?>
            <!-- Logs Grid (Cards) -->
            <div id="logsGrid" class="columns is-multiline">
                <?php foreach ($logs as $log): ?>
                <div class="column is-12-mobile is-6-tablet is-4-desktop log-card" 
                     data-message="<?= strtolower(e($log['message'])) ?>"
                     data-level="<?= strtolower($log['level']) ?>">
                    <div class="card">
                        <div class="card-content">
                            <div class="media">
                                <div class="media-left">
                                    <?php
                                    $levelIcons = [
                                        'info'    => 'fa-info-circle',
                                        'warning' => 'fa-exclamation-triangle',
                                        'error'   => 'fa-times-circle',
                                        'debug'   => 'fa-bug',
                                    ];
                                    $levelColors = [
                                        'info'    => 'has-text-info',
                                        'warning' => 'has-text-warning',
                                        'error'   => 'has-text-danger',
                                        'debug'   => 'has-text-grey',
                                    ];
                                    $icon = $levelIcons[$log['level']] ?? 'fa-file-alt';
                                    $color = $levelColors[$log['level']] ?? 'has-text-grey';
                                    ?>
                                    <span class="icon is-large <?= $color ?>">
                                        <i class="fas <?= $icon ?> fa-2x"></i>
                                    </span>
                                </div>
                                <div class="media-content">
                                    <p class="title is-6">
                                        <?php
                                        $tagColors = [
                                            'info'    => 'is-info',
                                            'warning' => 'is-warning',
                                            'error'   => 'is-danger',
                                            'debug'   => 'is-dark',
                                        ];
                                        $tagColor = $tagColors[$log['level']] ?? 'is-light';
                                        ?>
                                        <span class="tag <?= $tagColor ?>"><?= e(strtoupper($log['level'])) ?></span>
                                        <span class="tag is-light ml-1">ID: <?= $log['id'] ?></span>
                                    </p>
                                    <p class="subtitle is-7 has-text-grey mt-1">
                                        <span class="icon-text">
                                            <span class="icon"><i class="far fa-clock"></i></span>
                                            <span><?= e($log['created_at'] ?? $log['timestamp'] ?? '') ?></span>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="content">
                                <p class="has-text-grey-dark">
                                    <?= e(strlen($log['message']) > 100 ? substr($log['message'], 0, 100) . '...' : $log['message']) ?>
                                </p>
                                
                                <?php if (!empty($log['context'])): ?>
                                    <?php 
                                    $context = is_string($log['context']) ? json_decode($log['context'], true) : $log['context'];
                                    if (is_array($context)): 
                                    ?>
                                        <div class="tags are-small mt-2">
                                            <?php foreach (array_slice(array_keys($context), 0, 3) as $key): ?>
                                                <span class="tag is-light">
                                                    <span class="icon"><i class="fas fa-tag"></i></span>
                                                    <span><?= e($key) ?></span>
                                                </span>
                                            <?php endforeach; ?>
                                            <?php if (count($context) > 3): ?>
                                                <span class="tag is-light">+<?= count($context) - 3 ?> more</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <a href="/logs/<?= $log['id'] ?>" class="button is-small is-fullwidth is-info is-outlined mt-3">
                                    <span class="icon"><i class="fas fa-eye"></i></span>
                                    <span>View Details</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- No Results Message -->
            <div id="noResults" class="notification is-warning" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i> No logs found matching your search criteria.
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="pagination is-centered mt-5" role="navigation" aria-label="pagination">
                    <a href="/logs?page=<?= max(1, $currentPage - 1) ?>" 
                       class="pagination-previous <?= $currentPage <= 1 ? 'is-disabled' : '' ?>"
                       <?= $currentPage <= 1 ? 'disabled' : '' ?>>
                        Previous
                    </a>
                    <a href="/logs?page=<?= min($totalPages, $currentPage + 1) ?>" 
                       class="pagination-next <?= $currentPage >= $totalPages ? 'is-disabled' : '' ?>"
                       <?= $currentPage >= $totalPages ? 'disabled' : '' ?>>
                        Next page
                    </a>
                    <ul class="pagination-list">
                        <?php
                        // Show first page
                        if ($currentPage > 3):
                        ?>
                            <li><a href="/logs?page=1" class="pagination-link">1</a></li>
                            <?php if ($currentPage > 4): ?>
                                <li><span class="pagination-ellipsis">&hellip;</span></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php
                        // Show pages around current page
                        $start = max(1, $currentPage - 2);
                        $end = min($totalPages, $currentPage + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <li>
                                <a href="/logs?page=<?= $i ?>" 
                                   class="pagination-link <?= $i === $currentPage ? 'is-current' : '' ?>"
                                   <?= $i === $currentPage ? 'aria-current="page"' : '' ?>>
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php
                        // Show last page
                        if ($currentPage < $totalPages - 2):
                        ?>
                            <?php if ($currentPage < $totalPages - 3): ?>
                                <li><span class="pagination-ellipsis">&hellip;</span></li>
                            <?php endif; ?>
                            <li><a href="/logs?page=<?= $totalPages ?>" class="pagination-link"><?= $totalPages ?></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
            
            <!-- Summary -->
            <div class="has-text-centered has-text-grey mt-4">
                <p>
                    Showing <?= count($logs) ?> of <?= $totalLogs ?> log entries
                    <?php if ($totalPages > 1): ?>
                        (Page <?= $currentPage ?> of <?= $totalPages ?>)
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const levelFilter = document.getElementById('levelFilter');
    const logCards = document.querySelectorAll('.log-card');
    const noResults = document.getElementById('noResults');
    const pagination = document.querySelector('.pagination');
    const summary = document.querySelector('.has-text-centered.has-text-grey');
    
    function filterLogs() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const levelValue = levelFilter ? levelFilter.value.toLowerCase() : '';
        let visibleCount = 0;
        
        logCards.forEach(card => {
            const message = card.dataset.message || '';
            const level = card.dataset.level || '';
            
            const matchesSearch = !searchTerm || message.includes(searchTerm);
            const matchesLevel = !levelValue || level === levelValue;
            
            if (matchesSearch && matchesLevel) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }
        
        // Hide pagination and summary when filtering
        if (pagination) {
            pagination.style.display = (searchTerm || levelValue) ? 'none' : '';
        }
        if (summary) {
            summary.style.display = (searchTerm || levelValue) ? 'none' : '';
        }
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', filterLogs);
    }
    if (levelFilter) {
        levelFilter.addEventListener('change', filterLogs);
    }
});
</script>