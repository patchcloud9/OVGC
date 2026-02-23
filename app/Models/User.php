<?php

namespace App\Models;

/**
 * User Model
 * 
 * Represents a user in the system.
 * 
 * Usage:
 *   $user = User::find(1);
 *   $users = User::all();
 *   $user = User::create([
 *       'name' => 'John Doe',
 *       'email' => 'john@example.com',
 *       'password' => password_hash('secret', PASSWORD_DEFAULT)
 *   ]);
 */
class User extends Model
{
    protected string $table = 'users';
    
    protected array $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];
    
    protected bool $timestamps = true;
    
    /**
     * Find user by email
     */
    public static function findByEmail(string $email): ?array
    {
        return static::findWhere(['email' => $email]);
    }
    
    /**
     * Get users by role
     */
    public static function getByRole(string $role): array
    {
        return static::where(['role' => $role]);
    }
    
    /**
     * Verify password (for authentication)
     */
    public static function verifyPassword(string $email, string $password): ?array
    {
        $user = static::findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return null;
    }
}
