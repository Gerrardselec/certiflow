<?php
/**
 * Direct Database Connection Test
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/direct_db_test.php
 */

// Define application path constant
define('APP_PATH', __DIR__ . '/app');

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to output messages
function output($message, $success = true) {
    echo ($success ? "✅ " : "❌ ") . $message . "\n";
}

echo "===== Direct Database Connection Test =====\n\n";

// Test direct PDO connection
try {
    // Load the database configuration file directly
    $config = require_once APP_PATH . '/Config/database.php';
    
    // Get database credentials from config
    $connectionName = $config['default'];
    $dbConfig = $config['connections'][$connectionName];
    
    $host = $dbConfig['host'];
    $port = $dbConfig['port'];
    $database = $dbConfig['database'];
    $username = $dbConfig['username'];
    $password = $dbConfig['password'];
    $charset = $dbConfig['charset'] ?? 'utf8mb4';
    
    echo "Connection details:\n";
    echo "- Host: $host\n";
    echo "- Database: $database\n";
    echo "- Username: $username\n";
    echo "- Using password: " . (empty($password) ? "No" : "Yes") . "\n\n";
    
    // Create DSN
    $dsn = "{$dbConfig['driver']}:host={$host};port={$port};dbname={$database};charset={$charset}";
    
    // Connect directly using PDO
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    
    output("Direct PDO connection successful");
    
    // Check if we can list tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        output("Database tables found: " . implode(', ', $tables));
    } else {
        output("No tables found in database");
    }
    
    // Test one of the tables if available
    if (count($tables) > 0) {
        $tableName = $tables[0]; // Use the first available table
        $stmt = $pdo->query("DESCRIBE {$tableName}");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        output("Table '{$tableName}' has columns: " . implode(', ', $columns));
    }
    
} catch (PDOException $e) {
    output("Database connection failed: " . $e->getMessage(), false);
}

echo "\n===== Test Complete =====\n";