<?php
namespace App\Core;

/**
 * Base Model Class
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Core/Model.php
 * All model classes should extend this base class
 */
abstract class Model
{
    /**
     * @var Database The database instance
     */
    protected Database $db;
    
    /**
     * @var string The table name
     */
    protected string $table;
    
    /**
     * @var string The primary key field
     */
    protected string $primaryKey = 'id';
    
    /**
     * @var array The allowed fields for mass assignment
     */
    protected array $fillable = [];
    
    /**
     * @var array The validation rules
     */
    protected array $rules = [];
    
    /**
     * @var array Validation errors
     */
    protected array $errors = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Get database instance
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all records
     *
     * @param array $columns Columns to select
     * @return array
     */
    public function all(array $columns = ['*']): array
    {
        $query = "SELECT " . implode(', ', $columns) . " FROM {$this->table}";
        return $this->db->query($query)->fetchAll();
    }
    
    /**
     * Find a record by ID
     *
     * @param int $id The record ID
     * @param array $columns Columns to select
     * @return object|null
     */
    public function find(int $id, array $columns = ['*']): ?object
    {
        $query = "SELECT " . implode(', ', $columns) . " FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->query($query, ['id' => $id])->fetch();
    }
    
    /**
     * Find records by a specific field
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @param array $columns Columns to select
     * @return array
     */
    public function findBy(string $field, $value, array $columns = ['*']): array
    {
        $query = "SELECT " . implode(', ', $columns) . " FROM {$this->table} WHERE {$field} = :value";
        return $this->db->query($query, ['value' => $value])->fetchAll();
    }
    
    /**
     * Create a new record
     *
     * @param array $data The data to insert
     * @return int|bool The last insert ID or false on failure
     */
    public function create(array $data)
    {
        // Filter data to only include fillable fields
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        // Validate data
        if (!$this->validate($data)) {
            return false;
        }
        
        // Prepare fields and placeholders
        $fields = array_keys($data);
        $placeholders = array_map(function ($field) {
            return ":{$field}";
        }, $fields);
        
        // Build query
        $query = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                  VALUES (" . implode(', ', $placeholders) . ")";
        
        // Execute query
        $this->db->query($query, $data);
        
        // Return last insert ID
        return $this->db->lastInsertId();
    }
    
    /**
     * Update a record
     *
     * @param int $id The record ID
     * @param array $data The data to update
     * @return bool Success status
     */
    public function update(int $id, array $data): bool
    {
        // Filter data to only include fillable fields
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        // Validate data
        if (!$this->validate($data, false)) {
            return false;
        }
        
        // Prepare fields
        $fields = array_map(function ($field) {
            return "{$field} = :{$field}";
        }, array_keys($data));
        
        // Add ID to data
        $data['id'] = $id;
        
        // Build query
        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = :id";
        
        // Execute query
        return $this->db->query($query, $data)->rowCount() > 0;
    }
    
    /**
     * Delete a record
     *
     * @param int $id The record ID
     * @return bool Success status
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->query($query, ['id' => $id])->rowCount() > 0;
    }
    
    /**
     * Validate data against rules
     *
     * @param array $data The data to validate
     * @param bool $requireAll Whether to require all fields
     * @return bool Validation status
     */
    protected function validate(array $data, bool $requireAll = true): bool
    {
        // Reset errors
        $this->errors = [];
        
        // Loop through validation rules
        foreach ($this->rules as $field => $rules) {
            // Skip if field is not in data and not required
            if (!isset($data[$field]) && !$requireAll) {
                continue;
            }
            
            // Get field value
            $value = $data[$field] ?? null;
            
            // Split rules by pipe
            $rulesList = explode('|', $rules);
            
            // Apply each rule
            foreach ($rulesList as $rule) {
                // Check if rule has parameters
                if (strpos($rule, ':') !== false) {
                    list($ruleName, $ruleParams) = explode(':', $rule, 2);
                    $ruleParams = explode(',', $ruleParams);
                } else {
                    $ruleName = $rule;
                    $ruleParams = [];
                }
                
                // Apply rule
                $methodName = 'validate' . ucfirst($ruleName);
                if (method_exists($this, $methodName)) {
                    $valid = $this->$methodName($field, $value, $ruleParams);
                    if (!$valid) {
                        // Rule failed, stop processing this field
                        break;
                    }
                }
            }
        }
        
        // Return true if no errors
        return empty($this->errors);
    }
    
    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Add a validation error
     *
     * @param string $field The field name
     * @param string $message The error message
     */
    protected function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }
    
    /**
     * Validate required field
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @param array $params Rule parameters
     * @return bool Validation status
     */
    protected function validateRequired(string $field, $value, array $params): bool
    {
        if (empty($value)) {
            $this->addError($field, "{$field} is required");
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate string length
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @param array $params Rule parameters [min, max]
     * @return bool Validation status
     */
    protected function validateLength(string $field, $value, array $params): bool
    {
        $min = $params[0] ?? 0;
        $max = $params[1] ?? PHP_INT_MAX;
        
        if (strlen($value) < $min || strlen($value) > $max) {
            $this->addError($field, "{$field} must be between {$min} and {$max} characters");
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate email format
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @param array $params Rule parameters
     * @return bool Validation status
     */
    protected function validateEmail(string $field, $value, array $params): bool
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "{$field} must be a valid email address");
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate numeric value
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @param array $params Rule parameters
     * @return bool Validation status
     */
    protected function validateNumeric(string $field, $value, array $params): bool
    {
        if (!is_numeric($value)) {
            $this->addError($field, "{$field} must be a number");
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate unique field
     *
     * @param string $field The field name
     * @param mixed $value The field value
     * @param array $params Rule parameters [except_id]
     * @return bool Validation status
     */
    protected function validateUnique(string $field, $value, array $params): bool
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE {$field} = :value";
        $bindings = ['value' => $value];
        
        // Exclude current record if provided
        if (!empty($params[0])) {
            $query .= " AND {$this->primaryKey} != :id";
            $bindings['id'] = $params[0];
        }
        
        $result = $this->db->query($query, $bindings)->fetch();
        
        if ($result->count > 0) {
            $this->addError($field, "{$field} must be unique");
            return false;
        }
        
        return true;
    }
}