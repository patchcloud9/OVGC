<?php

namespace App\Models;

/**
 * Log Model
 * 
 * Represents a log entry in the system.
 */
class Log extends Model
{
    protected string $table = 'logs';
    
    protected array $fillable = [
        'level',
        'message',
        'context',
    ];
    
    protected bool $timestamps = false;  // Only created_at, no updated_at
    
    /**
     * Get logs by level
     */
    public static function getByLevel(string $level): array
    {
        return static::where(['level' => $level]);
    }
    
    /**
     * Get recent logs
     */
    public static function recent(int $limit = 10): array
    {
        $instance = new static();
        
        $sql = "SELECT * FROM {$instance->table} ORDER BY created_at DESC LIMIT ?";
        return $instance->getDatabase()->fetchAll($sql, [$limit]);
    }
    
    /**
     * Create a log entry with JSON context
     */
    public static function log(string $level, string $message, array $context = []): array
    {
        return static::create([
            'level' => $level,
            'message' => $message,
            'context' => json_encode($context),
        ]);
    }
}
