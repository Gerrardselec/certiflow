<?php
namespace App\Core;

/**
 * Main Application Class
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Core/App.php
 * This class bootstraps the application and handles the request lifecycle
 */
class App
{
    /**
     * @var Router The router instance
     */
    private Router $router;
    
    /**
     * @var Controller The active controller
     */
    private $controller;
    
    /**
     * @var string The action to call on the controller
     */
    private string $action;
    
    /**
     * @var array The parameters to pass to the action
     */
    private array $params = [];
    
    /**
     * @var array The configuration array
     */
    private array $config;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Load configuration
        $this->loadConfig();
        
        // Create router
        $this->router = new Router();
        
        // Set default controller and action
        $this->controller = $this->config['routing']['default_controller'];
        $this->action = $this->config['routing']['default_action'];
    }
    
    /**
     * Load application configuration
     */
    private function loadConfig(): void
    {
        // Load config file with explicit path
        $configFile = APP_PATH . '/Config/config.php';  // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Config/config.php
        $this->config = require_once $configFile;
        
        // Set error reporting based on environment
        if ($this->config['app']['environment'] === 'development') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        }
        
        // Set timezone
        date_default_timezone_set($this->config['app']['timezone']);
    }
    
    /**
     * Run the application
     */
    public function run(): void
    {
        // Parse the URL
        $this->parseUrl();
        
        // Get controller class
        $controllerClass = $this->config['routing']['controller_namespace'] . $this->controller;
        
        // Check if controller exists
        if (!class_exists($controllerClass)) {
            // Controller not found, use default error controller
            $controllerClass = $this->config['routing']['controller_namespace'] . 'ErrorController';
            $this->action = 'notFound';
        }
        
        // Create controller instance
        $this->controller = new $controllerClass();
        
        // Check if method exists
        if (!method_exists($this->controller, $this->action)) {
            // Method not found, use default error method
            $this->action = 'notFound';
        }
        
        // Call the controller method with parameters
        call_user_func_array([$this->controller, $this->action], $this->params);
    }
    
    /**
     * Parse the URL to extract controller, action and parameters
     */
    private function parseUrl(): void
    {
        if (isset($_GET['url'])) {
            // Trim and sanitize the URL
            $url = trim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            
            // Split URL into parts
            $urlParts = explode('/', $url);
            
            // Set controller if provided
            if (isset($urlParts[0]) && !empty($urlParts[0])) {
                $this->controller = ucfirst($urlParts[0]) . 'Controller';
            }
            
            // Set action if provided
            if (isset($urlParts[1]) && !empty($urlParts[1])) {
                $this->action = $urlParts[1];
            }
            
            // Set parameters
            $this->params = array_slice($urlParts, 2);
        }
    }
    
    /**
     * Get the configuration array
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}