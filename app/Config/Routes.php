<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route - redirects to homepage
$routes->get('/', 'Home::index');

// Main application routes
$routes->get('/home', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Authentication Routes
$routes->get('/register', 'Auth::register');     // Show registration form
$routes->post('/register', 'Auth::register');    // Process registration
$routes->get('/login', 'Auth::login');           // Show login form
$routes->post('/login', 'Auth::login');          // Process login
$routes->get('/logout', 'Auth::logout');         // Logout user
$routes->get('/dashboard', 'Auth::dashboard');   // User dashboard (fallback)

// Role-based Dashboard Routes
$routes->group('admin', function($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('users', 'AdminController::manageUsers');
    $routes->post('users/create', 'AdminController::createUser');
    $routes->post('users/update', 'AdminController::updateUser');
    $routes->post('users/delete', 'AdminController::deleteUser');
    $routes->get('courses', 'AdminController::manageCourses');
    $routes->post('courses/create', 'AdminController::createCourse');
    $routes->post('courses/update', 'AdminController::updateCourse');
    $routes->post('courses/delete', 'AdminController::deleteCourse');
    $routes->get('reports', 'AdminController::viewReports');
    // Material upload routes for admin
    $routes->get('course/(:num)/upload', 'Materials::upload/$1');
    $routes->post('course/(:num)/upload', 'Materials::upload/$1');
});

$routes->group('teacher', function($routes) {
    $routes->get('dashboard', 'TeacherController::dashboard');
    $routes->get('courses', 'TeacherController::manageCourses');
    $routes->get('course/(:num)/students', 'TeacherController::viewCourseStudents/$1');
    $routes->post('course/student/add', 'TeacherController::addStudentToCourse');
    $routes->post('course/student/remove', 'TeacherController::removeStudentFromCourse');
    $routes->get('assignments', 'TeacherController::manageAssignments');
    $routes->get('assignments/create', 'TeacherController::createAssignment');
    $routes->get('students', 'TeacherController::viewStudents');
    $routes->get('reviews', 'TeacherController::pendingReviews');
    $routes->get('gradebook', 'TeacherController::gradebook');
    $routes->get('announcements', 'TeacherController::announcements');
    $routes->get('cleanup-duplicates', 'TeacherController::cleanupDuplicates');
    // Enrollment approval routes
    $routes->get('pending-enrollments', 'TeacherController::viewPendingEnrollments');
    $routes->post('enrollment/approve', 'TeacherController::approveEnrollment');
    $routes->post('enrollment/reject', 'TeacherController::rejectEnrollment');
    // Material upload routes for teachers (uses same controller as admin)
    $routes->get('course/(:num)/upload', 'Materials::upload/$1');
    $routes->post('course/(:num)/upload', 'Materials::upload/$1');
});

$routes->group('student', function($routes) {
    $routes->get('dashboard', 'StudentController::dashboard');
    $routes->get('courses', 'StudentController::viewCourses');
    $routes->get('course/(:num)', 'StudentController::viewCourse/$1');
    $routes->get('assignments', 'StudentController::viewAssignments');
    $routes->get('grades', 'StudentController::viewGrades');
    $routes->get('profile', 'StudentController::profile');
    $routes->get('materials', 'StudentController::viewMaterials');
});

// Materials Routes (accessible to all authenticated users based on role)
$routes->get('materials/download/(:num)', 'Materials::download/$1');
$routes->get('materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('materials/approve/(:num)', 'Materials::approve/$1');
$routes->get('materials/reject/(:num)', 'Materials::reject/$1');
$routes->get('materials/pending', 'Materials::pending');

// Course Enrollment Routes 
$routes->post('/course/enroll', 'Course::enroll');
$routes->get('/course/available', 'Course::getAvailableCourses');

// Course Search Routes
$routes->get('/courses', 'Course::index');
$routes->get('/courses/search', 'Course::search');
$routes->post('/courses/search', 'Course::search');

$routes->get('/debug/database', 'DebugController::checkDatabase');

// Notification Routes (AJAX API endpoints)
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');
$routes->post('/notifications/mark_all_read', 'Notifications::mark_all_as_read');
