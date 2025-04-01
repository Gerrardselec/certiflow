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
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', 'certiflow'),
            'username' => env('DB_USERNAME', 'dbuser'),
            'password' => env('DB_PASSWORD', 'dbpass'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => 'InnoDB',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ],
        ],
        
        'testing' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', 3306),
            'database' => env('DB_DATABASE', 'certiflow') . '_testing',
            'username' => env('DB_USERNAME', 'dbuser'),
            'password' => env('DB_PASSWORD', 'dbpass'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => 'InnoDB',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ],
        ],
    ],
    
    // Database logging settings
    'logging' => [
        'enabled' => env('DB_LOGGING', false),
        'log_queries' => env('DB_LOG_QUERIES', false),
        'slow_query_threshold' => env('DB_SLOW_THRESHOLD', 1.0), // in seconds
    ],
];