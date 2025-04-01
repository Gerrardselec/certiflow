<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * QueryBuilder Class
 * 
 * Provides a fluent interface for building database queries
 */
class QueryBuilder
{
    /**
     * @var Database The database instance
     */
    private Database $db;
    
    /**
     * @var string The query string
     */
    private string $query = '';
    
    /**
     * @var array The query parameters
     */
    private array $params = [];
    
    /**
     * @var string The last insert ID
     */
    private string $lastInsertId = '';
    
    /**
     * @var int The number of affected rows
     */
    private int $rowCount = 0;
    
    /**
     * QueryBuilder constructor
     * 
     * @param Database|null $db
     */
    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }
    
    /**
     * Start a SELECT query
     * 
     * @param string $columns
     * @return self
     */
    public function select(string $columns = '*'): self
    {
        $this->query = "SELECT {$columns}";
        $this->params = [];
        return $this;
    }
    
    /**
     * Specify the FROM clause
     * 
     * @param string $table
     * @param string $alias
     * @return self
     */
    public function from(string $table, string $alias = ''): self
    {
        $this->query .= " FROM {$table}";
        
        if ($alias) {
            $this->query .= " AS {$alias}";
        }
        
        return $this;
    }
    
    /**
     * Add a JOIN clause
     * 
     * @param string $table
     * @param string $condition
     * @param string $type
     * @return self
     */
    public function join(string $table, string $condition, string $type = 'INNER'): self
    {
        $this->query .= " {$type} JOIN {$table} ON {$condition}";
        return $this;
    }
    
    /**
     * Add a LEFT JOIN clause
     * 
     * @param string $table
     * @param string $condition
     * @return self
     */
    public function leftJoin(string $table, string $condition): self
    {
        return $this->join($table, $condition, 'LEFT');
    }
    
    /**
     * Add a RIGHT JOIN clause
     * 
     * @param string $table
     * @param string $condition
     * @return self
     */
    public function rightJoin(string $table, string $condition): self
    {
        return $this->join($table, $condition, 'RIGHT');
    }
    
    /**
     * Add a WHERE clause
     * 
     * @param string $condition
     * @param array $params
     * @return self
     */
    public function where(string $condition, array $params = []): self
    {
        if (strpos($this->query, 'WHERE') === false) {
            $this->query .= " WHERE {$condition}";
        } else {
            $this->query .= " AND {$condition}";
        }
        
        $this->params = array_merge($this->params, $params);
        return $this;
    }
    
    /**
     * Add an OR WHERE clause
     * 
     * @param string $condition
     * @param array $params
     * @return self
     */
    public function orWhere(string $condition, array $params = []): self
    {
        if (strpos($this->query, 'WHERE') === false) {
            $this->query .= " WHERE {$condition}";
        } else {
            $this->query .= " OR {$condition}";
        }
        
        $this->params = array_merge($this->params, $params);
        return $this;
    }
    
    /**
     * Add an ORDER BY clause
     * 
     * @param string $columns
     * @return self
     */
    public function orderBy(string $columns): self
    {
        $this->query .= " ORDER BY {$columns}";
        return $this;
    }
    
    /**
     * Add a GROUP BY clause
     * 
     * @param string $columns
     * @return self
     */
    public function groupBy(string $columns): self
    {
        $this->query .= " GROUP BY {$columns}";
        return $this;
    }
    
    /**
     * Add a HAVING clause
     * 
     * @param string $condition
     * @param array $params
     * @return self
     */
    public function having(string $condition, array $params = []): self
    {
        $this->query .= " HAVING {$condition}";
        $this->params = array_merge($this->params, $params);
        return $this;
    }
    
    /**
     * Add a LIMIT clause
     * 
     * @param int $limit
     * @param int $offset
     * @return self
     */
    public function limit(int $limit, int $offset = 0): self
    {
        if ($offset > 0) {
            $this->query .= " LIMIT {$offset}, {$limit}";
        } else {
            $this->query .= " LIMIT {$limit}";
        }
        
        return $this;
    }
    
    /**
     * Start an INSERT query
     * 
     * @param string $table
     * @return self
     */
    public function insert(string $table): self
    {
        $this->query = "INSERT INTO {$table}";
        $this->params = [];
        return $this;
    }
    
    /**
     * Specify columns and values for an INSERT
     * 
     * @param array $data
     * @return self
     */
    public function values(array $data): self
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $this->query .= " (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $this->params = array_merge($this->params, $values);
        
        return $this;
    }
    
    /**
     * Start an UPDATE query
     * 
     * @param string $table
     * @return self
     */
    public function update(string $table): self
    {
        $this->query = "UPDATE {$table}";
        $this->params = [];
        return $this;
    }
    
    /**
     * Specify columns and values for an UPDATE
     * 
     * @param array $data
     * @return self
     */
    public function set(array $data): self
    {
        $parts = [];
        
        foreach ($data as $column => $value) {
            $parts[] = "{$column} = ?";
            $this->params[] = $value;
        }
        
        $this->query .= " SET " . implode(', ', $parts);
        
        return $this;
    }
    
    /**
     * Start a DELETE query
     * 
     * @param string $table
     * @return self
     */
    public function delete(string $table): self
    {
        $this->query = "DELETE FROM {$table}";
        $this->params = [];
        return $this;
    }
    
    /**
     * Execute the query and return all results as associative arrays
     * 
     * @return array
     */
    public function fetch(): array
    {
        try {
            // Execute the query
            $dbResult = $this->db->query($this->query, $this->params);
            $this->rowCount = $this->db->rowCount();
            
            // Get all results
            $results = [];
            while ($obj = $dbResult->fetch()) {
                $results[] = $this->objectToArray($obj);
            }
            
            return $results;
        } catch (PDOException $e) {
            $this->handleError($e);
            return [];
        }
    }
    
    /**
     * Execute the query and return a single result as an associative array
     * 
     * @return array|null
     */
    public function fetchOne(): ?array
    {
        try {
            // Execute the query
            $dbResult = $this->db->query($this->query, $this->params);
            $this->rowCount = $this->db->rowCount();
            
            // Get single result
            $obj = $dbResult->fetch();
            
            if ($obj === null) {
                return null;
            }
            
            return $this->objectToArray($obj);
        } catch (PDOException $e) {
            $this->handleError($e);
            return null;
        }
    }
    
    /**
     * Execute the query and return the first column of the first row
     * 
     * @return mixed
     */
    public function fetchValue()
    {
        try {
            // Execute the query
            $dbResult = $this->db->query($this->query, $this->params);
            $this->rowCount = $this->db->rowCount();
            
            // Get single result
            $obj = $dbResult->fetch();
            
            if ($obj === null) {
                return null;
            }
            
            $array = $this->objectToArray($obj);
            return reset($array); // Return first value
        } catch (PDOException $e) {
            $this->handleError($e);
            return null;
        }
    }
    
    /**
     * Convert a stdClass object to an associative array
     * 
     * @param object $obj
     * @return array
     */
    private function objectToArray(object $obj): array
    {
        return json_decode(json_encode($obj), true);
    }
    
    /**
     * Execute a non-select query
     * 
     * @return bool
     */
    public function execute(): bool
    {
        try {
            $this->db->query($this->query, $this->params);
            $this->rowCount = $this->db->rowCount();
            
            if (strpos($this->query, 'INSERT') === 0) {
                $this->lastInsertId = $this->db->lastInsertId();
            }
            
            return true;
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }
    
    /**
     * Get the last insert ID
     * 
     * @return string
     */
    public function getLastInsertId(): string
    {
        return $this->lastInsertId;
    }
    
    /**
     * Get the number of rows affected by the last query
     * 
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }
    
    /**
     * Get the raw query (for debugging)
     * 
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }
    
    /**
     * Get the parameters (for debugging)
     * 
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
    
    /**
     * Handle database errors
     * 
     * @param PDOException $e
     * @throws \Exception
     */
    private function handleError(PDOException $e): void
    {
        // Log the error
        error_log("QueryBuilder Error: " . $e->getMessage());
        error_log("Query: " . $this->query);
        error_log("Params: " . print_r($this->params, true));
        
        // In development, throw the exception for debugging
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            throw $e;
        }
        
        // In production, throw a generic database error
        throw new \Exception("A database error occurred. Please try again later.");
    }
}