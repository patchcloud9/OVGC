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

<?php
// compute current path and whether we’re on the homepage before rendering nav
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$currentPath = rtrim($currentPath, '/');
if ($currentPath === '') {
    $currentPath = '/';
}
$isHome = $currentPath === '/';
?>

<nav class="navbar is-primary<?php if ($isHome) echo ' homepage'; ?>" role="navigation" aria-label="main navigation">
    <div class="container">
        <div class="navbar-brand">
            <?php
            // (already computed above)

            // gather both logo settings
            $homepageLogo   = theme_setting('homepage_logo_path');
            $secondaryLogo  = theme_setting('secondary_logo_path');

            // choose which image to show initially
            if ($isHome) {
                $logo = $homepageLogo;
            } else {
                // other pages default to secondary logo, falling back to homepage
                $logo = $secondaryLogo ?: $homepageLogo;
            }

            $siteName = theme_setting('site_name');
            ?>
            <a class="navbar-item has-text-weight-bold logo-text" href="/">
                <?php if ($logo): ?>
                    <?php if ($isHome): ?>
                        <!-- primary logo shown on homepage initially -->
                        <img class="site-logo primary-logo" src="<?= e($homepageLogo) ?>" alt="<?= APP_NAME ?>">
                        <?php if ($secondaryLogo): ?>
                            <!-- secondary logo hidden until scroll -->
                            <img class="site-logo secondary-logo" src="<?= e($secondaryLogo) ?>" alt="<?= APP_NAME ?>">
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- non-home pages just render the chosen logo -->
                        <img class="site-logo primary-logo" src="<?= e($logo) ?>" alt="<?= APP_NAME ?>">
                    <?php endif; ?>
                <?php else: ?>
                    <?= APP_NAME ?>
                <?php endif; ?>

                <?php if (!$isHome && !empty($siteName)): ?>
                    <span class="site-title ml-2"><?= e($siteName) ?></span>
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


            </div>
        </div>
    </div>
</nav>

<!-- Overlay for mobile nav -->
<div id="navOverlay" class="nav-overlay" aria-hidden="true"></div>

<!-- ARIA live region for announcements -->
<div id="navLive" class="sr-only" aria-live="polite" aria-atomic="true"></div> 
