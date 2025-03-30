<?php
/**
 * Helper functions
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Helpers/helpers.php
 * This file contains useful helper functions for the application
 */

/**
 * Dump and die - Debug function
 *
 * @param mixed $data The data to dump
 * @return void
 */
function dd($data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Get the base URL
 *
 * @param string $path The path to append
 * @return string The complete URL
 */
function url(string $path = ''): string
{
    $baseUrl = BASE_URL;
    
    if (!empty($path)) {
        // Ensure path starts with a slash
        if ($path[0] !== '/') {
            $path = '/' . $path;
        }
        
        return $baseUrl . $path;
    }
    
    return $baseUrl;
}

/**
 * Get asset URL
 *
 * @param string $path The asset path
 * @return string The complete asset URL
 */
function asset(string $path): string
{
    $assetsUrl = ASSETS_URL;
    
    // Ensure path starts with a slash
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }
    
    return $assetsUrl . $path;
}

/**
 * Escape HTML
 *
 * @param string $string The string to escape
 * @return string The escaped string
 */
function e(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date
 *
 * @param string $date The date string
 * @param string $format The date format
 * @return string The formatted date
 */
function formatDate(string $date, string $format = DATE_FORMAT): string
{
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Generate a random string
 *
 * @param int $length The length of the string
 * @return string The random string
 */
function randomString(int $length = 10): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

/**
 * Create a slug from a string
 *
 * @param string $string The string to slugify
 * @return string The slug
 */
function slugify(string $string): string
{
    // Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($string)));
    
    // Remove leading and trailing hyphens
    $slug = trim($slug, '-');
    
    return $slug;
}

/**
 * Write to log file
 *
 * @param string $message The message to log
 * @param string $level The log level
 * @return void
 */
function logMessage(string $message, string $level = 'info'): void
{
    $date = date('Y-m-d H:i:s');
    // Explicit path to log file
    $logFile = STORAGE_PATH . '/logs/' . date('Y-m-d') . '.log';  // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/storage/logs/YYYY-MM-DD.log
    
    $logLine = "[{$date}] [{$level}] {$message}" . PHP_EOL;
    
    file_put_contents($logFile, $logLine, FILE_APPEND);
}

/**
 * Flash a message to the session
 *
 * @param string $key The message key
 * @param string $message The message
 * @return void
 */
function flash(string $key, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['flash'][$key] = $message;
}

/**
 * Get a flashed message from the session
 *
 * @param string $key The message key
 * @param bool $clear Whether to clear the message after retrieving
 * @return string|null The message or null if not found
 */
function getFlash(string $key, bool $clear = true): ?string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $message = $_SESSION['flash'][$key] ?? null;
    
    if ($clear && $message !== null) {
        unset($_SESSION['flash'][$key]);
    }
    
    return $message;
}

/**
 * Check if a flashed message exists
 *
 * @param string $key The message key
 * @return bool Whether the message exists
 */
function hasFlash(string $key): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['flash'][$key]);
}

/**
 * Generate a CSRF token
 *
 * @return string The CSRF token
 */
function generateCsrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token
 *
 * @param string $token The token to validate
 * @return bool Whether the token is valid
 */
function validateCsrfToken(string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate a CSRF field
 *
 * @return string The HTML for a CSRF field
 */
function csrfField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}