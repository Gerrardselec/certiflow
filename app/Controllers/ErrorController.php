<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Error Controller
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Controllers/ErrorController.php
 * Handles error pages and responses
 */
class ErrorController extends Controller
{
    /**
     * 404 Not Found page
     */
    public function notFound()
    {
        http_response_code(404);
        $this->view('errors/404');
    }
    
    /**
     * 500 Server Error page
     */
    public function serverError()
    {
        http_response_code(500);
        $this->view('errors/500');
    }
    
    /**
     * Generic error page
     * 
     * @param string $message The error message
     * @param int $code The error code
     */
    public function index($message = '', $code = 400)
    {
        http_response_code($code);
        $this->view('errors/generic', [
            'message' => $message,
            'code' => $code
        ]);
    }
}