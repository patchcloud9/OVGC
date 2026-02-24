<?php
// Load theme settings
$theme = get_site_theme();
$primaryColor = $theme['primary_color'] ?? '#667eea';
$secondaryColor = $theme['secondary_color'] ?? '#764ba2';
$accentColor = $theme['accent_color'] ?? '#48c78e';
$headerStyle = $theme['header_style'] ?? 'static';
$cardStyle = $theme['card_style'] ?? 'default';

// helper to convert a hex color to rgba string with given alpha
function rgba_from_hex(string $hex, float $alpha = 1.0): string
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0]
             . $hex[1] . $hex[1]
             . $hex[2] . $hex[2];
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "rgba($r,$g,$b,$alpha)";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME) ?> - <?= APP_NAME ?></title>
    
    <?php if (!empty($theme['favicon_path'])): ?>
    <!-- Custom Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= e($theme['favicon_path']) ?>">
    <?php endif; ?>
    
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">

    <!-- Google Fonts: Playfair Display (headings) + Lato (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS (cache-busted using file modification time) -->
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/app.css') ?>">
    
    <style>
        /* Dynamic Theme Styles */
        :root {
            --primary-color: <?= e($primaryColor) ?>;
            --primary-hover-color: <?= e($theme['primary_color'] ?? '#667eea') ?>dd;
            --secondary-color: <?= e($secondaryColor) ?>;
            --accent-color: <?= e($accentColor) ?>;
            --danger-color: <?= e($theme['danger_color'] ?? '#f14668') ?>;
            --navbar-color: <?= e($theme['navbar_color'] ?? '#667eea') ?>;
            --navbar-hover-color: <?= e($theme['navbar_hover_color'] ?? '#ffffff') ?>;
            --navbar-text-color: <?= e($theme['navbar_text_color'] ?? '#ffffff') ?>;
            /* semi‑transparent version for a 75%‑opaque background */
            --navbar-color-alpha: <?= rgba_from_hex($theme['navbar_color'] ?? '#667eea', 0.75) ?>;
        }
        
        /* Hero gradient with primary color */
        .hero.is-primary {
            <?php if (!empty($theme['hero_background_image'])): ?>
            background-image: url('<?= e($theme['hero_background_image']) ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            <?php elseif (!empty($theme['hero_background_color'])): ?>
            background-color: <?= e($theme['hero_background_color']) ?>;
            <?php else: ?>
            background: var(--primary-color);
            <?php endif; ?>
        }
        
        /* Navbar with custom background color; use the alpha variant here so
           the nav is slightly translucent by default.  The solid opaque value
           is still available (e.g. .navbar.is-scrolled overrides it to white). */
        .navbar.is-primary {
            background-color: var(--navbar-color-alpha);
            background-image: none;
        }
        
        /* Navbar text color */
        .navbar.is-primary .navbar-item,
        .navbar.is-primary .navbar-link {
            color: var(--navbar-text-color) !important;
        }
        
        /* Hamburger menu color */
        .navbar.is-primary .navbar-burger span {
            background-color: var(--navbar-text-color) !important;
        }
        
        /* Navbar item hover effects */
        .navbar.is-primary .navbar-item:hover,
        .navbar.is-primary .navbar-link:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: var(--navbar-hover-color) !important;
        }
        
        /* Dropdown menu background */
        .navbar.is-primary .navbar-dropdown {
            background-color: white;
        }

        /* Dropdown items need dark text (override the white navbar-item color above) */
        .navbar.is-primary .navbar-dropdown .navbar-item {
            color: #4a4a4a !important;
        }

        /* Dropdown menu items hover */
        .navbar.is-primary .navbar-dropdown a.navbar-item:hover,
        .navbar.is-primary .navbar-dropdown button.navbar-item:hover {
            background-color: var(--navbar-color) !important;
            color: var(--navbar-hover-color) !important;
        }
        
        /* Prevent navbar-link from turning green when dropdown items are hovered/focused/active */
        .navbar.is-primary .navbar-item.has-dropdown:hover .navbar-link,
        .navbar.is-primary .navbar-item.has-dropdown:focus .navbar-link,
        .navbar.is-primary .navbar-item.has-dropdown:focus-within .navbar-link,
        .navbar.is-primary .navbar-item.has-dropdown.is-active .navbar-link,
        .navbar.is-primary .navbar-end .navbar-link:focus,
        .navbar.is-primary .navbar-end .navbar-link:active {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: var(--navbar-hover-color) !important;
        }
        
        /* Override Bulma's default link colors in navbar */
        .navbar.is-primary a.navbar-item:hover,
        .navbar.is-primary a.navbar-link:hover {
            color: var(--navbar-hover-color) !important;
        }
        
        /* Standard buttons (primary = standard action) */
        .button.is-primary,
        .button.is-link {
            background-color: var(--primary-color);
            border-color: transparent;
        }
        
        .button.is-primary:hover,
        .button.is-link:hover {
            background-color: var(--primary-hover-color);
            filter: brightness(1.1);
        }
        
        /* Low priority buttons (cancel, back) */
        .button.is-light {
            background-color: var(--secondary-color);
            color: #363636;
        }
        
        .button.is-light:hover {
            background-color: var(--secondary-color);
            filter: brightness(0.95);
        }
        
        /* Destructive/important actions */
        .button.is-danger {
            background-color: var(--danger-color);
            border-color: transparent;
            color: white;
        }
        
        .button.is-danger:hover {
            background-color: var(--danger-color);
            filter: brightness(0.9);
        }
        
        /* Success states and messages */
        .button.is-success,
        .tag.is-success,
        .notification.is-success {
            background-color: var(--accent-color);
        }
        
        /* Info messages and standard links */
        .notification.is-info,
        a:not(.button):not(.navbar-item):not(.card-footer-item) {
            color: var(--accent-color);
        }
        
        a:not(.button):not(.navbar-item):not(.card-footer-item):hover {
            color: var(--accent-color);
            filter: brightness(1.2);
        }
        
        /* Content wrapper */
        .content-wrapper {
            min-height: calc(100vh - 200px);
        }
        
        /* Card styles based on theme preference */
        <?php if ($cardStyle === 'elevated'): ?>
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.08);
        }
        <?php elseif ($cardStyle === 'flat'): ?>
        .card {
            border: none;
            box-shadow: none;
        }
        <?php endif; ?>
        
        /* Header style */
        <?php if ($headerStyle === 'fixed'): ?>
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 30;
        }
        
        body {
            padding-top: 52px; /* Height of navbar */
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php require BASE_PATH . '/app/Views/partials/nav.php'; ?>

    <?php
    // Determine current page path (exclude query string)
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // normalize: remove trailing slash except root
    $currentPath = rtrim($currentPath, '/');
    if ($currentPath === '') {
        $currentPath = '/';
    }
    $dismissedCookie = $_COOKIE['dismissed_banners'] ?? '';
    $dismissedIds = $dismissedCookie !== '' ? explode(',', $dismissedCookie) : [];

    // build top banner markup wrapped inside a banner-container (no inner container)
    $topHtml = '';
    $topBanners = \App\Models\PageBanner::forPage($currentPath, 'top');
    if (!empty($topBanners)) {
        $topHtml .= "<div class=\"banner-container\">";
        foreach ($topBanners as $b) {
            if (in_array($b['id'], $dismissedIds, true)) {
                continue;
            }
            $colour = $b['colour'] ?: 'info';
            $dismissHtml = $b['dismissable'] ? '<button class="delete"></button>' : '';
            $topHtml .= "<div class=\"banner notification is-$colour\" data-id=\"" . e($b['id']) . "\">$dismissHtml" . e($b['text']) . "</div>";
        }
        $topHtml .= "</div>";
    }
    ?>

    <!-- Main Content -->
    <main class="content-wrapper">
        <?php
        // buffer the view so we can inject banners just after the hero section
        ob_start();
        ?>
        <?= $content ?>
        <?php
        $body = ob_get_clean();
        if ($topHtml !== '') {
            // insert right after closing of the first section (hero or not)
            if (preg_match('/<\/section\s*>/i', $body, $match, PREG_OFFSET_CAPTURE)) {
                $pos = $match[0][1] + strlen($match[0][0]);
                $body = substr_replace($body, $topHtml, $pos, 0);
            } else {
                $body = $topHtml . $body;
            }
        }
        echo $body;
        ?>
    </main>
    
    <!-- Bottom Banners -->
    <?php
    // bottom-position banners
    $bottomBanners = \App\Models\PageBanner::forPage($currentPath, 'bottom');
    if (!empty($bottomBanners)) {
        echo "<div class=\"banner-container\">";
        foreach ($bottomBanners as $b) {
            if (in_array($b['id'], $dismissedIds, true)) {
                continue;
            }
            $colour = $b['colour'] ?: 'info';
            $dismissHtml = $b['dismissable'] ? '<button class="delete"></button>' : '';
            echo "<div class=\"banner notification is-$colour\" data-id=\"" . e($b['id']) . "\">$dismissHtml" . e($b['text']) . "</div>";
        }
        echo "</div>";
    }
    ?>

    <!-- Footer -->
    <?php
    $siteName = theme_setting('site_name') ?: APP_NAME;
    $footerEmail = theme_setting('gallery_contact_email');
    $footerTagline = theme_setting('footer_tagline');
    $currentYear = date('Y');
    ?>
    <footer class="footer has-background-dark has-text-light">
        <div class="container">
            <div class="columns">
                <!-- About Section -->
                <div class="column is-4">
                    <h3 class="title is-5 has-text-light"><?= e($siteName) ?></h3>
                    <?php if (!empty($footerTagline)): ?>
                        <p class="subtitle is-6 has-text-grey-light"><?= e($footerTagline) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($footerEmail)): ?>
                        <p class="mt-3">
                            <span class="icon-text">
                                <span class="icon has-text-info">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <span><a href="mailto:<?= e($footerEmail) ?>" class="has-text-light"><?= e($footerEmail) ?></a></span>
                            </span>
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Links (rendered from menu_items) -->
                <div class="column is-4">
                    <h3 class="title is-5 has-text-light">Quick Links</h3>
                    <ul>
                        <?php
                        try {
                            $menuLevel = \App\Models\MenuItem::getUserVisibilityLevel();
                            $menuStructure = \App\Models\MenuItem::getMenuStructure($menuLevel);
                        } catch (\Exception $e) {
                            $menuStructure = [];
                            if (defined('APP_DEBUG') && APP_DEBUG) {
                                error_log('Footer menu load failed: ' . $e->getMessage());
                            }
                        }

                        foreach ($menuStructure as $item) {
                            // If item has no children, render directly
                            if (empty($item['children'])) {
                                ?>
                                <li class="mt-2"><a href="<?= e($item['url']) ?>" class="has-text-light" <?= $item['open_new_tab'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>><?= e($item['title']) ?></a></li>
                                <?php
                            } else {
                                // Render parent if it has a URL
                                if (!empty($item['url'])) {
                                    ?>
                                    <li class="mt-2"><a href="<?= e($item['url']) ?>" class="has-text-light" <?= $item['open_new_tab'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>><?= e($item['title']) ?></a></li>
                                    <?php
                                }

                                // Render children as indented links
                                foreach ($item['children'] as $child) {
                                    ?>
                                    <li class="mt-2"><a href="<?= e($child['url']) ?>" class="has-text-light" <?= $child['open_new_tab'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>&nbsp;&nbsp;<?= e($child['title']) ?></a></li>
                                    <?php
                                }
                            }
                        }
                        ?>


                    </ul>
                </div>
                
                <!-- Copyright -->
                <div class="column is-4">
                    <h3 class="title is-5 has-text-light">Copyright</h3>
                    <p class="has-text-grey-light">
                        © <?= $currentYear ?> <?= e($siteName) ?>
                    </p>
                    <p class="has-text-grey-light mt-2">
                        All rights reserved.
                    </p>
                </div>
            </div>
            
            <hr class="has-background-grey-dark">
            
            <!-- Bottom Text -->
            <div class="content has-text-centered has-text-grey-light">
                <p class="is-size-7">
                    Built with a custom PHP MVC framework.
                </p>
            </div>
        </div>
    </footer>
    
    <!-- Custom JavaScript (cache-busted using file modification time) -->
    <script src="/assets/js/app.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/app.js') ?>"></script>
    <script>
        // banner dismiss logic: store in cookie so closed banners remain hidden
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.banner .delete').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var banner = this.closest('.banner');
                    if (!banner) return;
                    var id = banner.dataset.id;
                    if (id) {
                        // read existing cookie
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
    </script>
</body>
</html>
