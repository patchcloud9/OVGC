<?php

namespace Core;

use PDO;
use PDOException;

/**
 * Database Class
 * 
 * PDO wrapper for database operations with prepared statements.
 * Uses singleton pattern to ensure one connection per request.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    
    /**
     * Private constructor - use getInstance() instead
     */
    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            if (APP_DEBUG) {
                error_log("Database: Connected to " . DB_NAME);
            }
            
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new \Exception("Could not connect to database");
        }
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        
        return self::$instance;
    }
    
    /**
     * Get the PDO connection
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
    
    /**
     * Execute a query and return the PDOStatement
     * Use for SELECT queries
     * 
     * @param string $sql    SQL query with ? placeholders
     * @param array  $params Parameters to bind
     * @return \PDOStatement
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            if (APP_DEBUG) {
                error_log("Database Query: {$sql}");
            }
            
            return $stmt;
            
        } catch (PDOException $e) {
            error_log("Database Query Error: " . $e->getMessage());
            error_log("SQL: {$sql}");
            throw $e;
        }
    }
    
    /**
     * Execute a statement (INSERT, UPDATE, DELETE)
     * Returns number of affected rows
     * 
     * @param string $sql    SQL query with ? placeholders
     * @param array  $params Parameters to bind
     * @return int Number of affected rows
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Fetch a single row
     * 
     * @param string $sql    SQL query
     * @param array  $params Parameters
     * @return array|null
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
    
    /**
     * Fetch all rows
     * 
     * @param string $sql    SQL query
     * @param array  $params Parameters
     * @return array
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get the last inserted ID
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Begin a transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commit a transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }
    
    /**
     * Rollback a transaction
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
    
    /**
     * Check if currently in a transaction
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }
}
