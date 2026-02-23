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
        '/purchase'             => ['PurchaseController', 'index'],
        '/membership'           => ['MembershipController', 'index'],
        
        // Authentication routes
        '/login'                => ['AuthController', 'showLogin', ['guest']],
        '/register'             => ['AuthController', 'showRegister', ['guest']],
        
        // Admin Panel
        '/admin'                => ['AdminController', 'index', ['auth', 'role:admin']],
        
        // Theme Settings (Admin Only)
        '/admin/theme'          => ['ThemeController', 'index', ['auth', 'role:admin']],
        
        // Homepage Settings (Admin Only)
        '/admin/homepage'       => ['HomepageController', 'index', ['auth', 'role:admin']],


        // Purchase Page Settings (Admin Only)
        '/admin/purchase'       => ['PurchaseController', 'edit', ['auth', 'role:admin']],

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

        // Logs (Admin Only)
        '/logs'                 => ['LogController', 'index', ['auth', 'role:admin']],
        '/logs/(\d+)'           => ['LogController', 'show', ['auth', 'role:admin']],
        
        // Gallery (Public)
        '/gallery'              => ['GalleryController', 'index'],

        
        // Gallery Management (Admin Only)
        '/admin/gallery'        => ['GalleryController', 'adminIndex', ['auth', 'role:admin']],
        '/admin/gallery/(\d+)/edit' => ['GalleryController', 'edit', ['auth', 'role:admin']],

        // Menu Management (Admin Only)
        '/admin/menu'           => ['MenuController', 'index', ['auth', 'role:admin']],
        '/admin/menu/create'    => ['MenuController', 'create', ['auth', 'role:admin']],
        '/admin/menu/(\d+)/edit' => ['MenuController', 'edit', ['auth', 'role:admin']],
    ],
    
    'POST' => [
        // Authentication routes
        '/login'                => ['AuthController', 'login', ['guest', 'csrf', 'rate-limit:login,5,300']],
        '/register'             => ['AuthController', 'register', ['guest', 'csrf', 'rate-limit:register,3,600']],
        '/logout'               => ['AuthController', 'logout', ['auth', 'csrf']],
        
        // Theme Settings (Admin Only)
        '/admin/theme'          => ['ThemeController', 'update', ['auth', 'role:admin', 'csrf']],
        '/admin/theme/reset'    => ['ThemeController', 'reset', ['auth', 'role:admin', 'csrf']],
        
        // Homepage Settings (Admin Only)
        '/admin/homepage'       => ['HomepageController', 'update', ['auth', 'role:admin', 'csrf']],
        '/admin/homepage/clear-hero-image' => ['HomepageController', 'clearHeroImage', ['auth', 'role:admin', 'csrf']],
        '/admin/homepage/clear-bottom-image' => ['HomepageController', 'clearBottomImage', ['auth', 'role:admin', 'csrf']],

        // About Page Settings (Admin Only)
        '/admin/purchase'       => ['PurchaseController', 'update', ['auth', 'role:admin', 'csrf']],

        // User Management (Admin Only)
        '/admin/users'          => ['UserController', 'store', ['auth', 'role:admin', 'csrf', 'rate-limit:user-creation,3,300']],
        
        // Logs (Admin Only)
        '/logs/clear'           => ['LogController', 'clear', ['auth', 'role:admin', 'csrf']],
        '/logs/sync'            => ['LogController', 'sync', ['auth', 'role:admin', 'csrf']],
        
        // Gallery Management (Admin Only)
        '/admin/gallery'        => ['GalleryController', 'store', ['auth', 'role:admin', 'csrf']],
        '/admin/gallery/reorder' => ['GalleryController', 'reorder', ['auth', 'role:admin', 'csrf']],

        // Menu Management (Admin Only)
        '/admin/menu'           => ['MenuController', 'store', ['auth', 'role:admin', 'csrf']],
        '/admin/menu/reorder'   => ['MenuController', 'reorder', ['auth', 'role:admin', 'csrf']],
    ],
    
    'PUT' => [
        // User Management (Admin Only)
        '/admin/users/(\d+)'    => ['UserController', 'update', ['auth', 'role:admin', 'csrf']],
        
        // Gallery Management (Admin Only)
        '/admin/gallery/(\d+)' => ['GalleryController', 'update', ['auth', 'role:admin', 'csrf']],

        // Menu Management (Admin Only)
        '/admin/menu/(\d+)'    => ['MenuController', 'update', ['auth', 'role:admin', 'csrf']],
    ],
    
    'DELETE' => [
        '/admin/users/(\d+)'    => ['UserController', 'destroy', ['auth', 'role:admin', 'csrf']],
        '/admin/gallery/(\d+)'  => ['GalleryController', 'destroy', ['auth', 'role:admin', 'csrf']],
        '/admin/menu/(\d+)'     => ['MenuController', 'destroy', ['auth', 'role:admin', 'csrf']],
    ],
];
