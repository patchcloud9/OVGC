<?php
// Load menu structure from database
use App\Models\MenuItem;

try {
    $userVisibilityLevel = MenuItem::getUserVisibilityLevel();
    $menuStructure = MenuItem::getMenuStructure($userVisibilityLevel);
} catch (\Exception $e) {
    // Fallback to empty array if database fails
    $menuStructure = [];
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('Menu load failed: ' . $e->getMessage());
    }
}
?>

<nav class="navbar is-primary" role="navigation" aria-label="main navigation">
    <div class="container">
        <div class="navbar-brand">
            <?php
            $logo = theme_setting('logo_path');
            $siteName = theme_setting('site_name');
            ?>
            <a class="navbar-item has-text-weight-bold" href="/">
                <?php if ($logo): ?>
                    <!-- logo downsized 25% (52px → 39px) -->
                    <img src="<?= e($logo) ?>" alt="<?= APP_NAME ?>" style="max-height: 39px; width: auto;">
                <?php else: ?>
                    <?= APP_NAME ?>
                <?php endif; ?>
                <?php if ($siteName): ?>
                    <span class="ml-2" style="font-family: 'Playfair Display', serif; font-size: 1.1rem; letter-spacing: 0.02em;"><?= e($siteName) ?></span>
                <?php endif; ?>
            </a>
            
            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="mainNavbar">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        
        <div id="mainNavbar" class="navbar-menu">
            <div class="navbar-end">
                <?php foreach ($menuStructure as $item): ?>
                    <?php if (!empty($item['children'])): ?>
                        <!-- Dropdown Menu -->
                        <div class="navbar-item has-dropdown is-hoverable">
                            <button class="navbar-link">
                                <?php if (!empty($item['icon'])): ?>
                                    <span class="icon"><i class="<?= e($item['icon']) ?>"></i></span>
                                <?php endif; ?>
                                <?= e($item['title']) ?>
                            </button>
                            <div class="navbar-dropdown">
                                <?php foreach ($item['children'] as $child): ?>
                                    <a class="navbar-item" href="<?= e($child['url']) ?>" <?= $child['open_new_tab'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
                                        <?php if (!empty($child['icon'])): ?>
                                            <span class="icon"><i class="<?= e($child['icon']) ?>"></i></span>
                                        <?php endif; ?>
                                        <span><?= e($child['title']) ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Regular Menu Item -->
                        <a class="navbar-item" href="<?= e($item['url']) ?>" <?= $item['open_new_tab'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
                            <?php if (!empty($item['icon'])): ?>
                                <span class="icon"><i class="<?= e($item['icon']) ?>"></i></span>
                            <?php endif; ?>
                            <?= e($item['title']) ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- User Menu (Always System-Managed) -->
                <?php if (is_authenticated()): ?>
                    <?php $user = auth_user(); ?>
                    
                    <div class="navbar-item has-dropdown is-hoverable">
                        <button class="navbar-link"><?= e($user['name']) ?></button>
                        <div class="navbar-dropdown is-right">
                            <?php if (is_admin()): ?>
                                <a class="navbar-item" href="/admin">
                                    <span class="icon"><i class="fas fa-cog"></i></span>
                                    <span>Admin Panel</span>
                                </a>
                            <?php endif; ?>
                            <hr class="navbar-divider">
                            <form method="POST" action="/logout">
                                <?= csrf_field() ?>
                                <button type="submit" class="navbar-item" style="width: 100%; border: none; background: none; cursor: pointer; justify-content: flex-start; display: flex; align-items: center; font-family: inherit; font-size: inherit; padding: 0.5rem 0.75rem;">
                                    <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <a class="navbar-item" href="/login">Log in</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Overlay for mobile nav -->
<div id="navOverlay" class="nav-overlay" aria-hidden="true"></div>

<!-- ARIA live region for announcements -->
<div id="navLive" class="sr-only" aria-live="polite" aria-atomic="true"></div> 
