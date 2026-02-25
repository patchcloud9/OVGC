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
            // primary branding image used on homepage and nav
            $logo = theme_setting('homepage_logo_path');
            // secondary_logo_path is stored for later use if needed
            $secondaryLogo = theme_setting('secondary_logo_path');
            $siteName = theme_setting('site_name');
            ?>
            <a class="navbar-item has-text-weight-bold logo-text" href="/">
                <?php if ($logo): ?>
                    <!-- logo slightly larger than before -->
                    <img class="site-logo" src="<?= e($logo) ?>" alt="<?= APP_NAME ?>">
                <?php else: ?>
                    <?= APP_NAME ?>
                <?php endif; ?>
                <!-- site-title removed for now; text not needed -->
                <!--
                <span class="site-title ml-2">kanogan&nbsp;Valley&nbsp;Golf&nbsp;Course</span>
                -->
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


            </div>
        </div>
    </div>
</nav>

<!-- Overlay for mobile nav -->
<div id="navOverlay" class="nav-overlay" aria-hidden="true"></div>

<!-- ARIA live region for announcements -->
<div id="navLive" class="sr-only" aria-live="polite" aria-atomic="true"></div> 
