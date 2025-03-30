<?php
namespace App\Core;

/**
 * Router Class
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Core/Router.php
 * Handles routing of requests to appropriate controllers and actions
 */
class Router
{
    /**
     * @var array The registered routes
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];
    
    /**
     * @var array The route parameters
     */
    private array $params = [];
    
    /**
     * Register a GET route
     *
     * @param string $route The route pattern
     * @param array $params The controller and action
     * @return Router
     */
    public function get(string $route, array $params = []): Router
    {
        return $this->addRoute('GET', $route, $params);
    }
    
    /**
     * Register a POST route
     *
     * @param string $route The route pattern
     * @param array $params The controller and action
     * @return Router
     */
    public function post(string $route, array $params = []): Router
    {
        return $this->addRoute('POST', $route, $params);
    }
    
    /**
     * Register a PUT route
     *
     * @param string $route The route pattern
     * @param array $params The controller and action
     * @return Router
     */
    public function put(string $route, array $params = []): Router
    {
        return $this->addRoute('PUT', $route, $params);
    }
    
    /**
     * Register a DELETE route
     *
     * @param string $route The route pattern
     * @param array $params The controller and action
     * @return Router
     */
    public function delete(string $route, array $params = []): Router
    {
        return $this->addRoute('DELETE', $route, $params);
    }
    
    /**
     * Add a route to the routing table
     *
     * @param string $method The HTTP method
     * @param string $route The route pattern
     * @param array $params The controller and action
     * @return Router
     */
    private function addRoute(string $method, string $route, array $params): Router
    {
        // Convert route to regex pattern
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        $route = '/^' . $route . '$/i';
        
        // Add route to routing table
        $this->routes[$method][$route] = $params;
        
        return $this;
    }
    
    /**
     * Match the route to the routes in the routing table
     *
     * @param string $url The URL to match
     * @param string $method The HTTP method
     * @return bool Whether the route was matched
     */
    public function match(string $url, string $method): bool
    {
        // Check if method exists in routes
        if (!isset($this->routes[$method])) {
            return false;
        }
        
        // Loop through routes for this method
        foreach ($this->routes[$method] as $route => $params) {
            // Test route against URL
            if (preg_match($route, $url, $matches)) {
                // Get named capture group values
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                
                // Store parameters
                $this->params = $params;
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Dispatch the route
     *
     * @param string $url The URL to dispatch
     * @return void
     */
    public function dispatch(string $url): void
    {
        // Remove query string from URL
        $url = $this->removeQueryString($url);
        
        // Get HTTP method
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Override method if _method is set in POST
        if ($method === 'POST' && isset($_POST['_method'])) {
            if (in_array($_POST['_method'], ['PUT', 'DELETE'])) {
                $method = $_POST['_method'];
            }
        }
        
        // Match route
        if ($this->match($url, $method)) {
            // Get controller and action
            $controller = $this->params['controller'] ?? null;
            $action = $this->params['action'] ?? null;
            
            // Remove controller and action from params
            unset($this->params['controller'], $this->params['action']);
            
            // Check if controller and action are set
            if ($controller && $action) {
                // Get controller class with proper namespace
                $controllerClass = "App\\Controllers\\{$controller}Controller";
                
                // Check if controller exists
                if (class_exists($controllerClass)) {
                    // Create controller instance
                    $controllerInstance = new $controllerClass();
                    
                    // Check if action exists
                    if (method_exists($controllerInstance, $action)) {
                        // Call controller action with parameters
                        call_user_func_array([$controllerInstance, $action], $this->params);
                    } else {
                        // Action not found
                        throw new \Exception("Method {$action} not found in controller {$controllerClass}");
                    }
                } else {
                    // Controller not found
                    throw new \Exception("Controller {$controllerClass} not found");
                }
            } else {
                // Controller or action not specified
                throw new \Exception("Controller and action not specified");
            }
        } else {
            // Route not found
            throw new \Exception("No route matched for {$url}");
        }
    }
    
    /**
     * Remove the query string from the URL
     *
     * @param string $url The URL
     * @return string The URL without the query string
     */
    private function removeQueryString(string $url): string
    {
        $parts = explode('?', $url, 2);
        return $parts[0];
    }
    
    /**
     * Get all registered routes
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
    
    /**
     * Get the currently matched parameters
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}