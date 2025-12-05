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

$routes->get('admin/users', 'Admin::users');
$routes->get('admin/users/add', 'Admin::addUser');
$routes->post('admin/users/store', 'Admin::storeUser');
$routes->get('admin/users/edit/(:num)', 'Admin::editUser/$1');
$routes->post('admin/users/update/(:num)', 'Admin::updateUser/$1');
$routes->get('admin/users/delete/(:num)', 'Admin::deleteUser/$1');