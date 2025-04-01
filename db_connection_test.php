<?php
/**
 * Database Connection Test
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/db_connection_test.php
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

echo "===== Database Connection Test =====\n\n";

// First, make sure the env helper is loaded
if (file_exists(APP_PATH . '/Helpers/env.php')) {
    require_once APP_PATH . '/Helpers/env.php';
    output("Env helper loaded successfully");
} else {
    output("Error: Could not find env.php helper file", false);
    exit(1);
}

// Make sure the env function exists
if (!function_exists('env')) {
    output("Error: env() function not defined after loading helper", false);
    exit(1);
}

// Try to load the .env file to make sure it's working
echo "Checking .env values:\n";
echo "- DB_HOST: " . env('DB_HOST', 'not set') . "\n";
echo "- DB_USERNAME: " . env('DB_USERNAME', 'not set') . "\n";
echo "- DB_DATABASE: " . env('DB_DATABASE', 'not set') . "\n\n";

// Now load the database configuration
try {
    $config = require_once APP_PATH . '/Config/database.php';
    output("Database config loaded successfully");
    
    // Check if the config has the expected structure
    if (isset($config['default']) && isset($config['connections'])) {
        output("Database config has correct structure");
    } else {
        output("Error: Database config does not have the expected structure", false);
        print_r($config);
        exit(1);
    }
} catch (Exception $e) {
    output("Error loading database config: " . $e->getMessage(), false);
    exit(1);
}

// Try to connect to the database
try {
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
    echo "- Port: $port\n";
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
    
    output("Successfully connected to database");
    
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