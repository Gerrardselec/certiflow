<?php
/**
 * Bootstrap file
 */

// Define absolute base path for the server
define('BASE_PATH', dirname(__DIR__));

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