<?php

namespace App\Models;

use Core\Database;

/**
 * Model Base Class
 * 
 * All models extend this class to get CRUD functionality.
 * 
 * Usage:
 *   class User extends Model {
 *       protected string $table = 'users';
 *   }
 * 
 *   $user = User::find(1);
 *   $users = User::all();
 *   $user = User::create(['name' => 'John', 'email' => 'john@example.com']);
 */
abstract class Model
{
    protected Database $db;
    
    /**
     * Table name (override in child classes)
     */
    protected string $table = '';
    
    /**
     * Primary key column name
     */
    protected string $primaryKey = 'id';
    
    /**
     * Fillable columns (for mass assignment protection)
     * Empty array means all columns are fillable
     */
    protected array $fillable = [];
    
    /**
     * Whether to automatically manage created_at/updated_at
     */
    protected bool $timestamps = true;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        
        // If table name not set, derive from class name
        // UserModel -> users, Post -> posts
        if (empty($this->table)) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower($className) . 's';
        }
    }
    
    /**
     * Get database instance (for custom queries)
     */
    protected function getDatabase(): Database
    {
        return $this->db;
    }
    
    /**
     * Find a record by primary key
     * 
     * @param int $id
     * @return array|null
     */
    public static function find(int $id): ?array
    {
        $instance = new static();
        
        $sql = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ? LIMIT 1";
        return $instance->db->fetch($sql, [$id]);
    }
    
    /**
     * Get all records
     * 
     * @return array
     */
    public static function all(): array
    {
        $instance = new static();
        
        $sql = "SELECT * FROM {$instance->table}";
        return $instance->db->fetchAll($sql);
    }
    
    /**
     * Find records matching conditions
     * 
     * @param array $conditions ['column' => 'value', ...]
     * @return array
     */
    public static function where(array $conditions): array
    {
        $instance = new static();
        
        $whereClauses = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $whereClauses[] = "{$column} = ?";
            $params[] = $value;
        }
        
        $whereString = implode(' AND ', $whereClauses);
        $sql = "SELECT * FROM {$instance->table} WHERE {$whereString}";
        
        return $instance->db->fetchAll($sql, $params);
    }
    
    /**
     * Find first record matching conditions
     * 
     * @param array $conditions
     * @return array|null
     */
    public static function findWhere(array $conditions): ?array
    {
        $instance = new static();
        
        $whereClauses = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $whereClauses[] = "{$column} = ?";
            $params[] = $value;
        }
        
        $whereString = implode(' AND ', $whereClauses);
        $sql = "SELECT * FROM {$instance->table} WHERE {$whereString} LIMIT 1";
        
        return $instance->db->fetch($sql, $params);
    }
    
    /**
     * Create a new record
     * 
     * @param array $data ['column' => 'value', ...]
     * @return array The created record
     */
    public static function create(array $data): array
    {
        $instance = new static();
        
        // Filter fillable fields if specified
        if (!empty($instance->fillable)) {
            $data = array_intersect_key($data, array_flip($instance->fillable));
        }
        
        // Add timestamps
        if ($instance->timestamps) {
            $now = date('Y-m-d H:i:s');
            $data['created_at'] = $now;
            $data['updated_at'] = $now;
        }
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO {$instance->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $instance->db->execute($sql, array_values($data));
        
        $id = (int) $instance->db->lastInsertId();
        
        return static::find($id);
    }
    
    /**
     * Update a record by primary key
     * 
     * @param int   $id   Primary key value
     * @param array $data Data to update
     * @return bool Success
     */
    public static function update(int $id, array $data): bool
    {
        $instance = new static();
        
        // Filter fillable fields if specified
        if (!empty($instance->fillable)) {
            $data = array_intersect_key($data, array_flip($instance->fillable));
        }
        
        // Add updated_at timestamp
        if ($instance->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $setClauses = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setClauses[] = "{$column} = ?";
            $params[] = $value;
        }
        
        $params[] = $id;  // For WHERE clause
        
        $setString = implode(', ', $setClauses);
        $sql = "UPDATE {$instance->table} SET {$setString} WHERE {$instance->primaryKey} = ?";
        
        $affected = $instance->db->execute($sql, $params);
        return $affected > 0;
    }
    
    /**
     * Delete a record by primary key
     * 
     * @param int $id Primary key value
     * @return bool Success
     */
    public static function delete(int $id): bool
    {
        $instance = new static();
        
        $sql = "DELETE FROM {$instance->table} WHERE {$instance->primaryKey} = ?";
        $affected = $instance->db->execute($sql, [$id]);
        
        return $affected > 0;
    }
    
    /**
     * Count all records
     * 
     * @return int
     */
    public static function count(): int
    {
        $instance = new static();
        
        $sql = "SELECT COUNT(*) as count FROM {$instance->table}";
        $result = $instance->db->fetch($sql);
        
        return (int) $result['count'];
    }
}
