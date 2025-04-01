<?php
/**
 * Database Test File
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/tests/database_test.php
 * Tests the database connection and QueryBuilder functionality
 */

// Define the application path if not already defined
defined('APP_PATH') or define('APP_PATH', dirname(__DIR__) . '/app');

// Include the bootstrap file to load necessary components
require_once dirname(__DIR__) . '/app/bootstrap.php';

// Create a function to output results in a formatted way
function output($message, $success = true) {
    echo ($success ? "✅ " : "❌ ") . $message . "\n";
}

echo "===== Database Connection and QueryBuilder Test =====\n\n";

// Test 1: Database Connection
try {
    $db = \App\Core\Database::getInstance();
    output("Database connection successful");
} catch (Exception $e) {
    output("Database connection failed: " . $e->getMessage(), false);
    exit(1);
}

// Test 2: Create a temporary test table
$testTableName = 'test_' . time();
try {
    $sql = "CREATE TABLE {$testTableName} (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->query($sql);
    output("Test table '{$testTableName}' created successfully");
} catch (Exception $e) {
    output("Failed to create test table: " . $e->getMessage(), false);
    exit(1);
}

// Test 3: Test QueryBuilder - Insert data
try {
    $qb = new \App\Core\QueryBuilder();
    $insertResult = $qb->insert($testTableName)
        ->values([
            'name' => 'Test Record 1'
        ])
        ->execute();
    
    if ($insertResult) {
        $insertId = $qb->getLastInsertId();
        output("Record inserted successfully with ID: {$insertId}");
    } else {
        output("Failed to insert record", false);
    }
} catch (Exception $e) {
    output("QueryBuilder insert failed: " . $e->getMessage(), false);
    exit(1);
}

// Test 4: Test QueryBuilder - Select data
try {
    $qb = new \App\Core\QueryBuilder();
    $result = $qb->select('*')
        ->from($testTableName)
        ->fetchOne();
    
    if ($result && isset($result['name']) && $result['name'] === 'Test Record 1') {
        output("Record retrieved successfully: " . $result['name']);
    } else {
        output("Failed to retrieve record correctly", false);
        var_dump($result);
    }
} catch (Exception $e) {
    output("QueryBuilder select failed: " . $e->getMessage(), false);
    exit(1);
}

// Test 5: Test QueryBuilder - Update data
try {
    $qb = new \App\Core\QueryBuilder();
    $updateResult = $qb->update($testTableName)
        ->set(['name' => 'Updated Test Record'])
        ->where('id = ?', [1])
        ->execute();
    
    if ($updateResult) {
        output("Record updated successfully");
    } else {
        output("Failed to update record", false);
    }
} catch (Exception $e) {
    output("QueryBuilder update failed: " . $e->getMessage(), false);
    exit(1);
}

// Test 6: Test QueryBuilder - Verify update
try {
    $qb = new \App\Core\QueryBuilder();
    $result = $qb->select('*')
        ->from($testTableName)
        ->where('id = ?', [1])
        ->fetchOne();
    
    if ($result && isset($result['name']) && $result['name'] === 'Updated Test Record') {
        output("Updated record retrieved successfully: " . $result['name']);
    } else {
        output("Failed to retrieve updated record correctly", false);
        var_dump($result);
    }
} catch (Exception $e) {
    output("QueryBuilder select after update failed: " . $e->getMessage(), false);
    exit(1);
}

// Test 7: Drop the test table
try {
    $sql = "DROP TABLE {$testTableName}";
    $db->query($sql);
    output("Test table '{$testTableName}' dropped successfully");
} catch (Exception $e) {
    output("Failed to drop test table: " . $e->getMessage(), false);
}

echo "\n===== Test Summary =====\n";
echo "All database and QueryBuilder tests completed.\n";
echo "If you see any failure messages above, please review the errors and fix them.\n";
echo "If all tests passed, the database setup is working correctly!\n";