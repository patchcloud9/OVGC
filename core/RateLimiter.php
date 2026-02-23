<?php

namespace Core;

/**
 * Rate Limiter
 * 
 * Simple token bucket rate limiter using session storage.
 * Prevents abuse by limiting number of requests per time period.
 * 
 * Usage:
 *   $limiter = new RateLimiter();
 *   if (!$limiter->attempt('login', 5, 60)) {
 *       // Too many attempts
 *   }
 * 
 * @package Core
 */
class RateLimiter
{
    /**
     * Attempt an action with rate limiting
     * 
     * @param string $key The identifier for this rate limit (e.g., 'login', 'contact-form')
     * @param int $maxAttempts Maximum number of attempts allowed
     * @param int $decaySeconds Time period in seconds
     * @return bool True if action is allowed, false if rate limit exceeded
     */
    public function attempt(string $key, int $maxAttempts = 5, int $decaySeconds = 60): bool
    {
        $this->clearExpired();
        
        $identifier = $this->getIdentifier($key);
        $now = time();
        
        // Initialize rate limit data if not exists
        if (!isset($_SESSION['rate_limits'][$identifier])) {
            $_SESSION['rate_limits'][$identifier] = [
                'attempts' => 0,
                'reset_at' => $now + $decaySeconds
            ];
        }
        
        $data = $_SESSION['rate_limits'][$identifier];
        
        // Reset if time window has passed
        if ($now >= $data['reset_at']) {
            $_SESSION['rate_limits'][$identifier] = [
                'attempts' => 1,
                'reset_at' => $now + $decaySeconds
            ];
            return true;
        }
        
        // Check if limit exceeded
        if ($data['attempts'] >= $maxAttempts) {
            return false;
        }
        
        // Increment attempts
        $_SESSION['rate_limits'][$identifier]['attempts']++;
        
        return true;
    }
    
    /**
     * Get remaining attempts for a key
     * 
     * @param string $key The rate limit key
     * @param int $maxAttempts Maximum attempts allowed
     * @return int Number of remaining attempts
     */
    public function remaining(string $key, int $maxAttempts = 5): int
    {
        $identifier = $this->getIdentifier($key);
        
        if (!isset($_SESSION['rate_limits'][$identifier])) {
            return $maxAttempts;
        }
        
        $data = $_SESSION['rate_limits'][$identifier];
        $now = time();
        
        // Reset if time window has passed
        if ($now >= $data['reset_at']) {
            return $maxAttempts;
        }
        
        return max(0, $maxAttempts - $data['attempts']);
    }
    
    /**
     * Get seconds until rate limit resets
     * 
     * @param string $key The rate limit key
     * @return int Seconds until reset (0 if not limited)
     */
    public function availableIn(string $key): int
    {
        $identifier = $this->getIdentifier($key);
        
        if (!isset($_SESSION['rate_limits'][$identifier])) {
            return 0;
        }
        
        $resetAt = $_SESSION['rate_limits'][$identifier]['reset_at'];
        $now = time();
        
        return max(0, $resetAt - $now);
    }
    
    /**
     * Clear rate limit for a specific key
     * 
     * @param string $key The rate limit key to clear
     * @return void
     */
    public function clear(string $key): void
    {
        $identifier = $this->getIdentifier($key);
        unset($_SESSION['rate_limits'][$identifier]);
    }
    
    /**
     * Generate unique identifier for rate limit
     * Combines key with IP address for per-IP limiting
     * 
     * @param string $key The rate limit key
     * @return string Unique identifier
     */
    private function getIdentifier(string $key): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return md5($key . ':' . $ip);
    }
    
    /**
     * Clear expired rate limit entries
     * 
     * @return void
     */
    private function clearExpired(): void
    {
        if (!isset($_SESSION['rate_limits'])) {
            $_SESSION['rate_limits'] = [];
            return;
        }
        
        $now = time();
        
        foreach ($_SESSION['rate_limits'] as $identifier => $data) {
            if ($now >= $data['reset_at']) {
                unset($_SESSION['rate_limits'][$identifier]);
            }
        }
    }
    
    /**
     * Hit the rate limiter without checking
     * Use when you want to record attempt regardless of limit
     * 
     * @param string $key The rate limit key
     * @param int $decaySeconds Time period in seconds
     * @return void
     */
    public function hit(string $key, int $decaySeconds = 60): void
    {
        $this->clearExpired();
        
        $identifier = $this->getIdentifier($key);
        $now = time();
        
        if (!isset($_SESSION['rate_limits'][$identifier])) {
            $_SESSION['rate_limits'][$identifier] = [
                'attempts' => 1,
                'reset_at' => $now + $decaySeconds
            ];
        } else {
            $data = $_SESSION['rate_limits'][$identifier];
            
            // Reset if time window has passed
            if ($now >= $data['reset_at']) {
                $_SESSION['rate_limits'][$identifier] = [
                    'attempts' => 1,
                    'reset_at' => $now + $decaySeconds
                ];
            } else {
                $_SESSION['rate_limits'][$identifier]['attempts']++;
            }
        }
    }
}
