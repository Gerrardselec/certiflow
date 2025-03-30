<?php
namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Database Class
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Core/Database.php
 * Singleton class for database connection and operations
 */
class Database
{
    /**
     * @var Database The singleton instance
     */
    private static ?Database $instance = null;
    
    /**
     * @var PDO The PDO connection
     */
    private PDO $pdo;
    
    /**
     * @var PDOStatement The last prepared statement
     */
    private PDOStatement $statement;
    
    /**
     * @var array The configuration array
     */
    private array $config;
    
    /**
     * @var array The parameters for the prepared statement
     */
    private array $params = [];
    
    /**
     * Private constructor
     */
    private function __construct()
    {
        // Load database configuration with explicit file path
        $dbConfigFile = APP_PATH . '/Config/database.php';  // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Config/database.php
        $this->config = require_once $dbConfigFile;
        
        // Get default connection configuration
        $connectionName = $this->config['default'];
        $connectionConfig = $this->config['connections'][$connectionName];
        
        // Create DSN
        $dsn = "{$connectionConfig['driver']}:host={$connectionConfig['host']};port={$connectionConfig['port']};dbname={$connectionConfig['database']};charset={$connectionConfig['charset']}";
        
        // Create PDO options
        $options = $connectionConfig['options'] ?? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        try {
            // Create PDO instance
            $this->pdo = new PDO($dsn, $connectionConfig['username'], $connectionConfig['password'], $options);
        } catch (PDOException $e) {
            // Log error and show message
            error_log("Database connection failed: {$e->getMessage()}");
            throw new PDOException("Database connection failed: {$e->getMessage()}");
        }
    }
    
    /**
     * Get singleton instance
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        
        return self::$instance;
    }
    
    /**
     * Prepare and execute a SQL query
     *
     * @param string $sql The SQL query
     * @param array $params The parameters for the query
     * @return Database
     */
    public function query(string $sql, array $params = []): Database
    {
        // Prepare statement
        $this->statement = $this->pdo->prepare($sql);
        
        // Store parameters
        $this->params = $params;
        
        // Execute statement
        try {
            $this->statement->execute($params);
        } catch (PDOException $e) {
            // Log error and show message
            error_log("Query execution failed: {$e->getMessage()} for SQL: {$sql}");
            throw new PDOException("Query execution failed: {$e->getMessage()}");
        }
        
        return $this;
    }
    
    /**
     * Fetch a single row
     *
     * @return object|null
     */
    public function fetch(): ?object
    {
        return $this->statement->fetch() ?: null;
    }
    
    /**
     * Fetch all rows
     *
     * @return array
     */
    public function fetchAll(): array
    {
        return $this->statement->fetchAll() ?: [];
    }
    
    /**
     * Get the number of affected rows
     *
     * @return int
     */
    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }
    
    /**
     * Get the last insert ID
     *
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Begin a transaction
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commit a transaction
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }
    
    /**
     * Rollback a transaction
     *
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
    
    /**
     * Get the PDO instance
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
    
    /**
     * Log a query
     *
     * @param string $sql The SQL query
     * @param array $params The parameters
     * @param float $executionTime The execution time
     */
    private function logQuery(string $sql, array $params, float $executionTime): void
    {
        if ($this->config['logging']['enabled'] && $this->config['logging']['log_queries']) {
            $paramString = json_encode($params);
            $message = "Query: {$sql} | Params: {$paramString} | Time: {$executionTime}s";
            
            // Log slow queries with a warning
            if ($executionTime > $this->config['logging']['slow_query_threshold']) {
                error_log("SLOW QUERY: {$message}");
            } else {
                error_log("QUERY: {$message}");
            }
        }
    }
}