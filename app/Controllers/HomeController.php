<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * Home Controller
 * 
 * File: /home/u320783783/domains/gerrardselectrical.co.uk/public_html/cert.gerrardselectrical.co.uk/app/Controllers/HomeController.php
 * Handles the homepage and default routes
 */
class HomeController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {
        $this->view('home/index', [
            'title' => 'CertiFlow - Electrical Testing Certificate Management'
        ]);
    }
    
    /**
     * Display the about page
     */
    public function about()
    {
        $this->view('home/about', [
            'title' => 'About CertiFlow'
        ]);
    }
    
    /**
     * Display the contact page
     */
    public function contact()
    {
        $this->view('home/contact', [
            'title' => 'Contact Us'
        ]);
    }
}