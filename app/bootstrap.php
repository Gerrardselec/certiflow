<?php
/**
 * Bootstrap file
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/bootstrap.php
 * This file initializes the application and loads required resources
 */

// Define absolute base path for the server
define('BASE_PATH', dirname(__DIR__));

// Load environment helper functions
require_once BASE_PATH . '/app/Helpers/env.php';

// Load environment variables from .env file
loadEnvFile(BASE_PATH . '/.env');

// Load constants - explicit file path
require_once BASE_PATH . '/app/Config/constants.php';

// Register Composer autoloader - explicit file path
require_once BASE_PATH . '/vendor/autoload.php';

// Load helpers - explicit file path
require_once BASE_PATH . '/app/Helpers/helpers.php';

// Initialize application
$app = new App\Core\App();

// Run application
$app->run();