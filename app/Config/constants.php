<?php
/**
 * Application constants
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Config/constants.php
 * Contains all constant values used throughout the application
 */

// Path constants with explicit server directory structure
// BASE_PATH is already defined in bootstrap.php
define('APP_PATH', BASE_PATH . '/app');                     // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app
define('PUBLIC_PATH', BASE_PATH . '/public');               // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/public
define('STORAGE_PATH', BASE_PATH . '/storage');             // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/storage
define('VIEWS_PATH', APP_PATH . '/Views');                  // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Views

// URL constants
define('BASE_URL', 'https://cert.gerrardselectrical.co.uk');
define('ASSETS_URL', BASE_URL . '/assets');

// User roles
define('ROLE_ADMIN', 1);
define('ROLE_ENGINEER', 2);
define('ROLE_OFFICE', 3);
define('ROLE_CUSTOMER', 4);

// Certificate types
define('CERT_TYPE_EICR', 1);
define('CERT_TYPE_INSTALLATION', 2);
define('CERT_TYPE_MINOR_WORKS', 3);

// Certificate status
define('CERT_STATUS_DRAFT', 'draft');
define('CERT_STATUS_ISSUED', 'issued');
define('CERT_STATUS_EXPIRED', 'expired');
define('CERT_STATUS_SUPERSEDED', 'superseded');

// Observation codes
define('OBS_CODE_C1', 'C1');
define('OBS_CODE_C2', 'C2');
define('OBS_CODE_C3', 'C3');
define('OBS_CODE_FI', 'FI');

// File upload limits
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB

// Date formats
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i:s');
define('MYSQL_DATE_FORMAT', 'Y-m-d');
define('MYSQL_DATETIME_FORMAT', 'Y-m-d H:i:s');

// Pagination
define('ITEMS_PER_PAGE', 20);

// Email constants
define('EMAIL_TYPE_CERTIFICATE', 'certificate');
define('EMAIL_TYPE_ACCOUNT', 'account');
define('EMAIL_TYPE_PASSWORD_RESET', 'password_reset');
define('EMAIL_TYPE_NOTIFICATION', 'notification');