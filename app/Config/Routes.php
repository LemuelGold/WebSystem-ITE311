<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::cantact');

// Register
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
// Login
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
//Logout and Dashboard
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');

// Course Enrollment
$routes->post('/course/enroll', 'Course::enroll');