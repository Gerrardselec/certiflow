<?php
/**
 * Main configuration file
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Config/config.php
 * Contains all application configuration settings
 */

return [
    // Application settings
    'app' => [
        'name' => 'CertiFlow',
        'version' => '1.0.0',
        'url' => 'https://cert.gerrardselectrical.co.uk',
        'environment' => 'development', // development, staging, production
        'debug' => true,
        'timezone' => 'Europe/London',
        'locale' => 'en_GB',
    ],
    
    // Security settings
    'security' => [
        'jwt_secret' => 'your_jwt_secret_key_here', // Change in production!
        'jwt_expiry' => 3600, // 1 hour in seconds
        'password_algorithm' => PASSWORD_BCRYPT,
        'password_options' => [
            'cost' => 12
        ],
        'session_expiry' => 1800, // 30 minutes in seconds
    ],
    
    // File storage settings with explicit directory paths
    'storage' => [
        'certificates_path' => BASE_PATH . '/storage/certificates/',  // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/storage/certificates/
        'temp_path' => BASE_PATH . '/storage/temp/',                  // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/storage/temp/
        'logs_path' => BASE_PATH . '/storage/logs/',                  // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/storage/logs/
        'max_upload_size' => 10 * 1024 * 1024, // 10MB in bytes
    ],
    
    // PDF generator settings with explicit file paths
    'pdf' => [
        'paper_size' => 'A4',
        'orientation' => 'portrait',
        'company_logo' => PUBLIC_PATH . '/assets/images/logo_for_pdf.png',  // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/public/assets/images/logo_for_pdf.png
        'fonts' => [
            'default' => 'helvetica',
        ],
    ],
    
    // Email settings
    'email' => [
        'from_address' => 'certificates@gerrardselectrical.co.uk',
        'from_name' => 'Gerrards Electrical Ltd',
        'admin_email' => 'admin@gerrardselectrical.co.uk',
        'smtp_host' => 'smtp.example.com',
        'smtp_port' => 587,
        'smtp_secure' => 'tls', // tls, ssl
        'smtp_auth' => true,
        'smtp_username' => 'your_smtp_username',
        'smtp_password' => 'your_smtp_password',
    ],
    
    // Routing settings
    'routing' => [
        'default_controller' => 'HomeController',
        'default_action' => 'index',
        'controller_namespace' => 'App\\Controllers\\',
    ],
];