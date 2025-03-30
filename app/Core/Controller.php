<?php
namespace App\Core;

/**
 * Base Controller Class
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Core/Controller.php
 * All controller classes should extend this base class
 */
abstract class Controller
{
    /**
     * @var array The configuration array
     */
    protected array $config = [];
    
    /**
     * @var array The request data
     */
    protected array $request = [];
    
    /**
     * @var array The session data
     */
    protected array $session = [];
    
    /**
     * @var Database|null The database instance
     */
    protected ?Database $db = null;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Load configuration with explicit file path and error handling
        $configFile = APP_PATH . '/Config/config.php';  // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Config/config.php
        
        if (file_exists($configFile)) {
            $configData = require $configFile;
            if (is_array($configData)) {
                $this->config = $configData;
            } else {
                // Log the error and use empty array
                error_log("Configuration file doesn't return an array: {$configFile}");
            }
        } else {
            // Log the error if file doesn't exist
            error_log("Configuration file not found: {$configFile}");
        }
        
        // Initialize request data
        $this->initRequest();
        
        // Initialize session
        $this->initSession();
        
        // Initialize database - with error handling
        try {
            $this->db = Database::getInstance();
        } catch (\Exception $e) {
            // Log database error but continue
            error_log("Database initialization error: " . $e->getMessage());
        }
    }
    
    /**
     * Initialize the request data
     */
    protected function initRequest(): void
    {
        // Merge GET and POST data
        $this->request = array_merge($_GET ?? [], $_POST ?? []);
        
        // Add JSON data if content type is application/json
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            $jsonData = json_decode($json, true);
            
            if ($jsonData) {
                $this->request = array_merge($this->request, $jsonData);
            }
        }
    }
    
    /**
     * Initialize the session
     */
    protected function initSession(): void
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Store session data
        $this->session = $_SESSION ?? [];
    }
    
    /**
     * Load a view file
     *
     * @param string $view The view file to load
     * @param array $data The data to pass to the view
     * @return void
     */
    protected function view(string $view, array $data = []): void
    {
        // Extract data to make variables available in view
        extract($data);
        
        // Build the full view path with explicit directory structure
        $viewPath = VIEWS_PATH . '/' . $view . '.php';  // /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Views/[view].php
        
        // Check if view file exists
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // View not found
            echo "<div style='color:red;'>Error: View file not found: {$viewPath}</div>";
            error_log("View file not found: {$viewPath}");
        }
    }
    
    /**
     * Redirect to another URL
     *
     * @param string $url The URL to redirect to
     * @return void
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Return JSON response
     *
     * @param mixed $data The data to encode as JSON
     * @param int $statusCode The HTTP status code
     * @return void
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Default error method for not found resources
     */
    public function notFound(): void
    {
        http_response_code(404);
        
        // Check if error view exists, otherwise show basic error
        $errorViewPath = VIEWS_PATH . '/errors/404.php';
        if (file_exists($errorViewPath)) {
            $this->view('errors/404');
        } else {
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>The requested page could not be found.</p>";
            echo "<p><a href='/'>Return to Home</a></p>";
        }
    }
}