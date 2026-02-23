<?php
/**
 * Simple PSR-4 Style Autoloader
 * 
 * When PHP encounters a class it doesn't know (like App\Controllers\HomeController),
 * this autoloader converts the namespace to a file path and loads it.
 * 
 * Namespace mapping:
 *   Core\     => /core/
 *   App\      => /app/
 */

spl_autoload_register(function (string $class): void {
    // Define namespace to directory mapping
    $namespaceMap = [
        'Core\\'  => BASE_PATH . '/core/',
        'App\\'   => BASE_PATH . '/app/',
    ];
    
    // Check each namespace prefix
    foreach ($namespaceMap as $prefix => $directory) {
        // Does the class use this namespace?
        $prefixLength = strlen($prefix);
        
        if (strncmp($prefix, $class, $prefixLength) === 0) {
            // Get the relative class name (without the namespace prefix)
            $relativeClass = substr($class, $prefixLength);
            
            // Convert namespace separators to directory separators
            // App\Controllers\HomeController => Controllers/HomeController
            $file = $directory . str_replace('\\', '/', $relativeClass) . '.php';
            
            // If the file exists, load it
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
    
    // For debugging: uncomment to see what classes couldn't be found
    // error_log("Autoloader: Could not find class {$class}");
});
