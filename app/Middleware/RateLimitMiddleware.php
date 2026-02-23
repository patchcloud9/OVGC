<?php

namespace App\Middleware;

use Core\Middleware;
use Core\RateLimiter;

/**
 * Rate Limiting Middleware
 * 
 * Throttles requests to prevent abuse. Uses token bucket algorithm.
 * 
 * Usage in routes:
 *   'POST' => [
 *       '/contact' => ['HomeController', 'contactSubmit', ['rate-limit:contact-form,5,60']],
 *       '/users' => ['UserController', 'store', ['rate-limit:user-creation,3,300']],
 *   ]
 * 
 * Parameters:
 *   rate-limit:KEY,MAX_ATTEMPTS,DECAY_SECONDS
 *   - KEY: Unique identifier for this rate limit
 *   - MAX_ATTEMPTS: Maximum attempts allowed
 *   - DECAY_SECONDS: Time window in seconds
 */
class RateLimitMiddleware extends Middleware
{
    public function handle(array $params = []): bool
    {
        if (count($params) < 3) {
            error_log("RateLimitMiddleware: Invalid parameters. Expected [key, max, decay]");
            return true;
        }
        
        [$key, $maxAttempts, $decaySeconds] = $params;
        
        $rateLimiter = new RateLimiter();
        
        if (!$rateLimiter->attempt($key, (int) $maxAttempts, (int) $decaySeconds)) {
            $availableIn = $rateLimiter->availableIn($key);
            
            // Return JSON for API-style responses
            $this->json([
                'success' => false,
                'message' => 'Too many attempts. Please try again later.',
                'retry_after' => $availableIn,
            ], 429);
            
            return false;
        }
        
        return true;
    }
}
