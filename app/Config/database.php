<?php
/**
 * Database configuration file
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Config/database.php
 * Contains all database connection settings
 */

return [
    'default' => 'mysql',
    
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'u320783783_certiflow',
            'username' => 'u320783783_certiflow',
            'password' => 'YourSecurePassword123!',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => 'InnoDB',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],
        
        'testing' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'u320783783_certiflow_test',
            'username' => 'u320783783_certiflow',
            'password' => 'YourSecurePassword123!',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => 'InnoDB',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],
    ],
    
    // Database logging settings
    'logging' => [
        'enabled' => false,
        'log_queries' => false,
        'slow_query_threshold' => 1.0, // in seconds
    ],
];