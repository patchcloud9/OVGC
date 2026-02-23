<section class="section" style="padding-top: 1.5rem;">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>
        
        <h1 class="title is-3">
            <i class="fas fa-crown"></i> Admin Panel
        </h1>
        <p class="subtitle">Welcome, <?= e(auth_user()['name']) ?>!</p>

        <!-- Pages -->
        <div class="box mt-5">
            <h2 class="title is-4"><i class="fas fa-file-alt"></i> Pages</h2>
            <div class="buttons">
                <a href="/admin/homepage" class="button is-primary">
                    <span class="icon"><i class="fas fa-home"></i></span>
                    <span>Homepage</span>
                </a>
                <a href="/admin/about" class="button is-primary">
                    <span class="icon"><i class="fas fa-user-circle"></i></span>
                    <span>About</span>
                </a>
                <a href="/admin/gallery" class="button is-primary">
                    <span class="icon"><i class="fas fa-images"></i></span>
                    <span>Gallery</span>
                </a>
                <a href="/admin/purchase" class="button is-primary">
                    <span class="icon"><i class="fas fa-shopping-cart"></i></span>
                    <span>Purchase</span>
                </a>
            </div>
        </div>

        <!-- Settings -->
        <div class="box mt-5">
            <h2 class="title is-4"><i class="fas fa-cog"></i> Settings</h2>
            <div class="buttons">
                <a href="/admin/theme" class="button is-primary">
                    <span class="icon"><i class="fas fa-palette"></i></span>
                    <span>Theme</span>
                </a>
                <a href="/admin/menu" class="button is-primary">
                    <span class="icon"><i class="fas fa-bars"></i></span>
                    <span>Menu</span>
                </a>
                <a href="/admin/users" class="button is-primary">
                    <span class="icon"><i class="fas fa-users-cog"></i></span>
                    <span>Users</span>
                </a>
                <a href="/logs" class="button is-primary">
                    <span class="icon"><i class="fas fa-list"></i></span>
                    <span>Logs</span>
                </a>
            </div>
        </div>
        
        <!-- Developer Tools -->
        <div class="box mt-5">
            <h2 class="title is-4"><i class="fas fa-wrench"></i> Developer Tools</h2>
            <div class="buttons">
                <a href="/debug" class="button is-light">
                    <span class="icon"><i class="fas fa-bug"></i></span>
                    <span>Debug Info</span>
                </a>
                <a href="/nonexistent" class="button is-light" target="_blank" rel="noopener noreferrer">
                    <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <span>Test 404</span>
                </a>
                <a href="/test-500" class="button is-light" target="_blank" rel="noopener noreferrer">
                    <span class="icon"><i class="fas fa-bomb"></i></span>
                    <span>Test 500</span>
                </a>
            </div>
        </div>
        
        <?php if (!empty($recentLogs)): ?>
        <!-- Recent Activity -->
        <div class="box mt-5">
            <h2 class="title is-4"><i class="fas fa-history"></i> Recent Activity</h2>
            <div class="table-container">
                <table class="table is-fullwidth is-striped">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Message</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLogs as $log): ?>
                        <tr>
                            <td>
                                <span class="tag <?= $log['level'] === 'error' ? 'is-danger' : ($log['level'] === 'warning' ? 'is-warning' : 'is-info') ?>">
                                    <?= e(ucfirst($log['level'])) ?>
                                </span>
                            </td>
                            <td><?= e($log['message']) ?></td>
                            <td>
                                <small class="has-text-grey">
                                    <?= isset($log['created_at']) ? e($log['created_at']) : e($log['timestamp'] ?? 'N/A') ?>
                                </small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <a href="/logs" class="button is-small is-primary">View All Logs →</a>
        </div>
        <?php endif; ?>
    </div>
</section>
