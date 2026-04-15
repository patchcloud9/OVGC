<?php
/**
 * Route Definitions
 * 
 * Format:
 *   'HTTP_METHOD' => [
 *       '/url/pattern'     => ['ControllerName', 'methodName'],
 *       '/with/(\d+)'      => ['ControllerName', 'methodName', ['middleware']],
 *   ]
 * 
 * Middleware:
 *   Third array element is optional middleware list:
 *   - 'csrf'                              - CSRF protection
 *   - 'auth'                              - Require authentication
 *   - 'guest'                             - Require NOT authenticated
 *   - 'rate-limit:key,max,seconds'        - Rate limiting
 *   - 'log-request'                       - Log the request
 * 
 * Examples:
 *   ['HomeController', 'index', ['log-request']]
 *   ['UserController', 'store', ['csrf', 'rate-limit:user-creation,3,300']]
 *   ['AdminController', 'index', ['auth', 'log-request']]
 * 
 * Common regex patterns:
 *   (\d+)       - One or more digits (for IDs)
 *   ([a-z]+)    - One or more lowercase letters
 *   ([a-z0-9-]+) - Lowercase letters, numbers, and hyphens (for slugs)
 *   (.+)        - Anything (be careful with this one)
 */

return [
    'GET' => [
        // Home routes
        '/'                     => ['HomeController', 'index'],
        '/contact'              => ['ContactController', 'index'],
        '/membership'           => ['MembershipController', 'index'],
        '/rates'                => ['RatesController', 'index'],
        
        // Authentication routes
        '/login'                => ['AuthController', 'showLogin', ['guest']],
        '/register'             => ['AuthController', 'showRegister', ['guest']],
        '/password/forgot'      => ['PasswordResetController', 'showForgotForm', ['guest']],
        '/password/reset'       => ['PasswordResetController', 'showResetForm',  ['guest']],
        
        // Admin Panel
        '/admin'                => ['AdminController', 'index', ['auth', 'role:admin']],

        // Admin Documentation (Admin Only)
        '/admin/docs'                => ['Admin\DocsController', 'index', ['auth', 'role:admin']],
        '/admin/docs/([a-z0-9-]+)'   => ['Admin\DocsController', 'show',  ['auth', 'role:admin']],
        
        // Theme Settings (Admin Only)
        '/admin/theme'          => ['ThemeController', 'index', ['auth', 'role:admin']],
        
        // Homepage Settings (Admin Only)
        '/admin/homepage'       => ['HomepageController', 'index', ['auth', 'role:admin']],


        // Purchase Page Settings (Admin Only)

        // User Management (Admin Only)
        '/admin/users'          => ['UserController', 'index', ['auth', 'role:admin']],
        '/admin/users/create'   => ['UserController', 'create', ['auth', 'role:admin']],
        '/admin/users/(\d+)/edit' => ['UserController', 'edit', ['auth', 'role:admin']],
        
        // Example with multiple parameters
        '/posts/(\d+)/comments/(\d+)' => ['PostController', 'showComment'], // /posts/5/comments/23
        
        // Debug route - shows how routing works (Admin Only)
        '/debug'                => ['HomeController', 'debug', ['auth', 'role:admin']],
        
        // Test error pages (Admin Only)
        '/test-500'             => ['HomeController', 'test500', ['auth', 'role:admin']],

        // Test Email (Admin Only)
        '/admin/test-email'     => ['AdminController', 'testEmail', ['auth', 'role:admin']],

        // Logs (Admin Only)
        '/logs'                 => ['LogController', 'index', ['auth', 'role:admin']],
        '/logs/(\d+)'           => ['LogController', 'show', ['auth', 'role:admin']],
        
        // Camera proxy (serves FTP image only when file is stable)
        '/camera/live'          => ['CameraController', 'live'],

        // Gallery (Public)
        '/gallery'              => ['GalleryController', 'index'],
        '/board-members'        => ['BoardController', 'index'],

        
        // Gallery Management (Admin Only)
        '/admin/gallery'        => ['GalleryController', 'adminIndex', ['auth', 'role:admin']],
        '/admin/gallery/(\d+)/edit' => ['GalleryController', 'edit', ['auth', 'role:admin']],

        // Rate Groups (Admin Only)
        '/admin/rates'                  => ['RateGroupController', 'index', ['auth', 'role:admin']],
        '/admin/rates/create'           => ['RateGroupController', 'create', ['auth', 'role:admin']],
        '/admin/rates/(\d+)/edit'      => ['RateGroupController', 'edit', ['auth', 'role:admin']],
        '/admin/rates/(\d+)/rates'     => ['RateController', 'index', ['auth', 'role:admin']],
        '/admin/rates/(\d+)/rates/create' => ['RateController', 'create', ['auth', 'role:admin']],
        '/admin/rates/(\d+)/rates/(\d+)/edit' => ['RateController', 'edit', ['auth', 'role:admin']],

        // Membership Groups (Admin Only)
        '/admin/membership'                 => ['MembershipGroupController', 'index', ['auth', 'role:admin']],
        '/admin/membership/create'          => ['MembershipGroupController', 'create', ['auth', 'role:admin']],
        '/admin/membership/(\d+)/edit'     => ['MembershipGroupController', 'edit', ['auth', 'role:admin']],
        '/admin/membership/(\d+)/items'    => ['MembershipItemController', 'index', ['auth', 'role:admin']],
        '/admin/membership/(\d+)/items/create' => ['MembershipItemController', 'create', ['auth', 'role:admin']],
        '/admin/membership/(\d+)/items/(\d+)/edit' => ['MembershipItemController', 'edit', ['auth', 'role:admin']],
        
        // Board Members (Admin Only)
        '/admin/board-members'                  => ['Admin\\BoardMemberController', 'index', ['auth', 'role:admin']],
        '/admin/board-members/create'           => ['Admin\\BoardMemberController', 'create', ['auth', 'role:admin']],
        '/admin/board-members/(\d+)/edit'      => ['Admin\\BoardMemberController', 'edit', ['auth', 'role:admin']],
        // Board Minutes (Admin Only)
        '/admin/board-minutes'                  => ['Admin\\BoardMinuteController', 'index', ['auth', 'role:admin']],
        '/admin/board-minutes/create'           => ['Admin\\BoardMinuteController', 'create', ['auth', 'role:admin']],
        '/admin/board-minutes/(\d+)/edit'      => ['Admin\\BoardMinuteController', 'edit', ['auth', 'role:admin']],
        // Banners (Admin Only)
        '/admin/banners'         => ['PageBannerController', 'index', ['auth', 'role:admin']],
        '/admin/banners/create'  => ['PageBannerController', 'create', ['auth', 'role:admin']],
        '/admin/banners/(\d+)/edit' => ['PageBannerController', 'edit', ['auth', 'role:admin']],
        // Menu Management (Admin Only)
        '/admin/menu'           => ['MenuController', 'index', ['auth', 'role:admin']],
        '/admin/menu/create'    => ['MenuController', 'create', ['auth', 'role:admin']],
        '/admin/menu/(\d+)/edit' => ['MenuController', 'edit', ['auth', 'role:admin']],

        // Results (Public)
        '/results'                             => ['ResultsController', 'index'],

        // Flyers (Public)
        '/flyers'                              => ['FlyerController', 'index'],

        // Events (Public)
        '/events'                              => ['EventController', 'index'],
        '/events/feed'                         => ['EventController', 'feed'],
        '/events/calendar.ics'                 => ['EventController', 'ical'],
        '/events/(\d+)'                        => ['EventController', 'show'],
        '/events/(\d+)/(\d{4}-\d{2}-\d{2})'   => ['EventController', 'showOccurrence'],

        // Flyers (Admin Only)
        '/admin/flyers'             => ['Admin\FlyerController', 'index',  ['auth', 'role:admin']],
        '/admin/flyers/create'      => ['Admin\FlyerController', 'create', ['auth', 'role:admin']],
        '/admin/flyers/(\d+)/edit'  => ['Admin\FlyerController', 'edit',   ['auth', 'role:admin']],

        // Events (Admin Only)
        '/admin/events'                                   => ['Admin\EventController', 'index',       ['auth', 'role:admin']],
        '/admin/events/create'                            => ['Admin\EventController', 'create',      ['auth', 'role:admin']],
        '/admin/events/(\d+)/edit'                        => ['Admin\EventController', 'edit',        ['auth', 'role:admin']],
        '/admin/events/(\d+)/cancel'                      => ['Admin\EventController', 'cancelForm',  ['auth', 'role:admin']],
        '/admin/events/(\d+)/results/(\d{4}-\d{2}-\d{2})' => ['Admin\EventController', 'resultsForm',  ['auth', 'role:admin']],
        '/admin/events/(\d+)/results'                      => ['Admin\EventController', 'resultsIndex', ['auth', 'role:admin']],
    ],
    
    'POST' => [
        // Authentication routes
        '/login'                => ['AuthController', 'login',          ['guest', 'csrf', 'rate-limit:login,5,300']],
        '/register'             => ['AuthController', 'register',       ['guest', 'csrf', 'rate-limit:register,3,600']],
        '/logout'               => ['AuthController', 'logout',         ['auth',  'csrf']],
        '/password/forgot'      => ['PasswordResetController', 'sendResetLink',  ['guest', 'csrf', 'rate-limit:password-reset,3,600']],
        '/password/reset'       => ['PasswordResetController', 'resetPassword',  ['guest', 'csrf']],

        // Contact form
        '/contact'              => ['ContactController', 'send', ['csrf', 'rate-limit:contact,5,300']],
        
        // Theme Settings (Admin Only)
        '/admin/theme'          => ['ThemeController', 'update', ['auth', 'role:admin', 'csrf']],
        '/admin/theme/reset'    => ['ThemeController', 'reset', ['auth', 'role:admin', 'csrf']],
        
        // Homepage Settings (Admin Only)
        '/admin/homepage'       => ['HomepageController', 'update', ['auth', 'role:admin', 'csrf']],
        '/admin/homepage/clear-hero-image' => ['HomepageController', 'clearHeroImage', ['auth', 'role:admin', 'csrf']],
        '/admin/homepage/clear-bottom-image' => ['HomepageController', 'clearBottomImage', ['auth', 'role:admin', 'csrf']],
        '/admin/homepage/clear-camera-image' => ['HomepageController', 'clearCameraImage', ['auth', 'role:admin', 'csrf']],

        // About Page Settings (Admin Only)

        // User Management (Admin Only)
        '/admin/users'          => ['UserController', 'store', ['auth', 'role:admin', 'csrf', 'rate-limit:user-creation,3,300']],
        
        // Test Email (Admin Only)
        '/admin/test-email'     => ['AdminController', 'sendTestEmail', ['auth', 'role:admin', 'csrf']],

        // Logs (Admin Only)
        '/logs/clear'           => ['LogController', 'clear', ['auth', 'role:admin', 'csrf']],
        '/logs/sync'            => ['LogController', 'sync', ['auth', 'role:admin', 'csrf']],
        
        // Gallery Management (Admin Only)
        '/admin/gallery'        => ['GalleryController', 'store', ['auth', 'role:admin', 'csrf']],
        '/admin/gallery/reorder' => ['GalleryController', 'reorder', ['auth', 'role:admin', 'csrf']],

        // Rate groups & rates (Admin Only)
        '/admin/rates'            => ['RateGroupController', 'store', ['auth', 'role:admin', 'csrf']],
        '/admin/rates/content'    => ['RateGroupController', 'updateContent', ['auth', 'role:admin', 'csrf']],
        '/admin/rates/(\d+)/rates' => ['RateController', 'store', ['auth', 'role:admin', 'csrf']],

        // Membership groups & items (Admin Only)
        '/admin/membership'       => ['MembershipGroupController', 'store', ['auth', 'role:admin', 'csrf']],
        '/admin/membership/content' => ['MembershipGroupController', 'updateContent', ['auth', 'role:admin', 'csrf']],
        '/admin/membership/(\d+)/items' => ['MembershipItemController', 'store', ['auth', 'role:admin', 'csrf']],
        
        // Board Members (Admin Only)
        '/admin/board-members'    => ['Admin\\BoardMemberController', 'store', ['auth', 'role:admin', 'csrf']],
        '/admin/board-minutes'    => ['Admin\\BoardMinuteController', 'store', ['auth', 'role:admin', 'csrf']],

        // Banners (Admin Only)
        '/admin/banners'          => ['PageBannerController', 'store', ['auth', 'role:admin', 'csrf']],

        // Menu Management (Admin Only)
        '/admin/menu'           => ['MenuController', 'store', ['auth', 'role:admin', 'csrf']],
        '/admin/menu/reorder'   => ['MenuController', 'reorder', ['auth', 'role:admin', 'csrf']],

        // Flyers (Admin Only)
        '/admin/flyers/create'      => ['Admin\FlyerController', 'store',   ['auth', 'role:admin', 'csrf']],
        '/admin/flyers/(\d+)/edit'  => ['Admin\FlyerController', 'update',  ['auth', 'role:admin', 'csrf']],
        '/admin/flyers/(\d+)/delete' => ['Admin\FlyerController', 'destroy', ['auth', 'role:admin', 'csrf']],

        // Events (Admin Only)
        '/admin/events/create'                             => ['Admin\EventController', 'store',        ['auth', 'role:admin', 'csrf']],
        '/admin/events/(\d+)/edit'                         => ['Admin\EventController', 'update',       ['auth', 'role:admin', 'csrf']],
        '/admin/events/(\d+)/cancel'                       => ['Admin\EventController', 'cancelStore',  ['auth', 'role:admin', 'csrf']],
        '/admin/events/(\d+)/restore'                      => ['Admin\EventController', 'restore',      ['auth', 'role:admin', 'csrf']],
        '/admin/events/(\d+)/delete'                       => ['Admin\EventController', 'destroy',      ['auth', 'role:admin', 'csrf']],
        '/admin/events/(\d+)/results/(\d{4}-\d{2}-\d{2})'         => ['Admin\EventController', 'resultsStore',   ['auth', 'role:admin', 'csrf']],
        '/admin/events/(\d+)/results/(\d{4}-\d{2}-\d{2})/delete' => ['Admin\EventController', 'resultsDestroy', ['auth', 'role:admin', 'csrf']],
    ],
    
    'PUT' => [
        // User Management (Admin Only)
        '/admin/users/(\d+)'    => ['UserController', 'update', ['auth', 'role:admin', 'csrf']],
        
        // Gallery Management (Admin Only)
        '/admin/gallery/(\d+)' => ['GalleryController', 'update', ['auth', 'role:admin', 'csrf']],

        // Rate groups & rates (Admin Only)
        '/admin/rates/(\d+)' => ['RateGroupController', 'update', ['auth', 'role:admin', 'csrf']],
        '/admin/rates/(\d+)/rates/(\d+)' => ['RateController', 'update', ['auth', 'role:admin', 'csrf']],

        // Membership groups & items (Admin Only)
        '/admin/membership/(\d+)' => ['MembershipGroupController', 'update', ['auth', 'role:admin', 'csrf']],
        '/admin/membership/(\d+)/items/(\d+)' => ['MembershipItemController', 'update', ['auth', 'role:admin', 'csrf']],
        
        // Board Members update
        '/admin/board-members/(\d+)' => ['Admin\\BoardMemberController', 'update', ['auth', 'role:admin', 'csrf']],
        '/admin/board-minutes/(\d+)' => ['Admin\\BoardMinuteController', 'update', ['auth', 'role:admin', 'csrf']],

        // Banners (Admin Only)
        '/admin/banners/(\d+)'    => ['PageBannerController', 'update', ['auth', 'role:admin', 'csrf']],

        // Menu Management (Admin Only)
        '/admin/menu/(\d+)'    => ['MenuController', 'update', ['auth', 'role:admin', 'csrf']],
    ],
    
    'DELETE' => [
        '/admin/users/(\d+)'    => ['UserController', 'destroy', ['auth', 'role:admin', 'csrf']],
        '/admin/gallery/(\d+)'  => ['GalleryController', 'destroy', ['auth', 'role:admin', 'csrf']],
        '/admin/rates/(\d+)'    => ['RateGroupController', 'destroy', ['auth', 'role:admin', 'csrf']],
        '/admin/rates/(\d+)/rates/(\d+)' => ['RateController', 'destroy', ['auth', 'role:admin', 'csrf']],
        '/admin/membership/(\d+)' => ['MembershipGroupController', 'destroy', ['auth', 'role:admin', 'csrf']],
        '/admin/membership/(\d+)/items/(\d+)' => ['MembershipItemController', 'destroy', ['auth', 'role:admin', 'csrf']],
        '/admin/board-members/(\d+)' => ['Admin\\BoardMemberController', 'destroy', ['auth', 'role:admin', 'csrf']],
        '/admin/board-minutes/(\d+)' => ['Admin\\BoardMinuteController', 'destroy', ['auth', 'role:admin', 'csrf']],
        '/admin/banners/(\d+)'   => ['PageBannerController', 'destroy', ['auth', 'role:admin', 'csrf']],
        '/admin/menu/(\d+)'     => ['MenuController', 'destroy', ['auth', 'role:admin', 'csrf']],
    ],
];
