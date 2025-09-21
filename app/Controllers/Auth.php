<?php

namespace App\Controllers;

/**
 * Auth Controller - Handles user authentication (login, register, logout, dashboard)
 */
class Auth extends BaseController
{
    protected $session;
    protected $validation;
    protected $db;

    public function __construct()
    {
        // Initialize session, validation, and database services
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
    }

    /**
     * User registration - handles both GET (show form) and POST (process form)
     */
    public function register()
    {
        // If user is already logged in, redirect to appropriate dashboard based on role
        if ($this->isLoggedIn()) {
            $role = $this->session->get('role');
            switch ($role) {
                case 'admin':
                    return redirect()->to(base_url('admin/dashboard'));
                case 'teacher':
                    return redirect()->to(base_url('teacher/dashboard'));
                case 'student':
                    return redirect()->to(base_url('student/dashboard'));
                default:
                    return redirect()->to(base_url('dashboard'));
            }
        }

        // Check if the form was submitted (POST request)
        if ($this->request->getMethod() === 'POST') {
            
            // Set validation rules for the form fields
            $rules = [
                'name'             => 'required|min_length[3]|max_length[100]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]'
            ];

            $messages = [
                'name' => [
                    'required'   => 'Name is required.',
                    'min_length' => 'Name must be at least 3 characters long.',
                    'max_length' => 'Name cannot exceed 100 characters.'
                ],
                'email' => [
                    'required'    => 'Email is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'is_unique'   => 'This email is already registered.'
                ],
                'password' => [
                    'required'   => 'Password is required.',
                    'min_length' => 'Password must be at least 6 characters long.'
                ],
                'password_confirm' => [
                    'required' => 'Password confirmation is required.',
                    'matches'  => 'Wrong password please type the same password.'
                ]
            ];

            // If validation passes
            if ($this->validate($rules, $messages)) {
                
                // Hash the password using password_hash() function
                $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

                // Prepare user data to match your table structure
                $userData = [
                    'name'       => $this->request->getPost('name'),
                    'email'      => $this->request->getPost('email'),
                    'password'   => $hashedPassword,
                    'role'       => 'student', // Automatic role assignment: all new registrations are students
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Save the user data to the users table
                $builder = $this->db->table('users');
                
                if ($builder->insert($userData)) {
                    $this->session->setFlashdata('success', 'Registration successful! Please login with your credentials.');
                    return redirect()->to(base_url('login'));
                } else {
                    $this->session->setFlashdata('error', 'Registration failed. Please try again.');
                }
            } else {
                // Validation failed - set errors and redirect back to form
                $this->session->setFlashdata('errors', $this->validation->getErrors());
                return redirect()->back()->withInput();
            }
        }

        return view('auth/register');
    }

    /**
     * User login - handles both GET (show form) and POST (authenticate user)
     */
    public function login()
    {
        // If user is already logged in, redirect to appropriate dashboard based on role
        if ($this->isLoggedIn()) {
            $role = $this->session->get('role');
            switch ($role) {
                case 'admin':
                    return redirect()->to(base_url('admin/dashboard'));
                case 'teacher':
                    return redirect()->to(base_url('teacher/dashboard'));
                case 'student':
                    return redirect()->to(base_url('student/dashboard'));
                default:
                    return redirect()->to(base_url('dashboard'));
            }
        }

        // Check for a POST request
        if ($this->request->getMethod() === 'POST') {
            
            // Rate limiting - prevent brute force attacks
            if (!$this->checkLoginAttempts()) {
                return view('auth/login');
            }
            
            // Get the login field (can be email or username)
            $login = $this->request->getPost('login');
            $password = $this->request->getPost('password');

            // Input validation and sanitization
            if (empty(trim($login)) || empty(trim($password))) {
                $this->session->setFlashdata('error', 'Please enter both login and password.');
                return view('auth/login');
            }

            // Sanitize inputs to prevent injection attacks
            $login = filter_var(trim($login), FILTER_SANITIZE_STRING);
            
            // Check for hardcoded admin login (temporary - should be moved to database)
            if ($login === 'admin' && $password === 'admin123') {
                $sessionData = [
                    'userID'     => 1,
                    'name'       => 'Administrator',
                    'email'      => 'admin@lms.com',
                    'role'       => 'admin',
                    'isLoggedIn' => true,
                    'loginTime'  => time() // Track login time for security
                ];
                $this->session->set($sessionData);
                $this->session->setFlashdata('success', 'Welcome back, Administrator!');
                
                // Log successful admin login
                log_message('info', 'Admin login successful from IP: ' . $this->request->getIPAddress());
                
                // Role-based redirection for admin
                return redirect()->to(base_url('admin/dashboard'));
            }

            // Check the database for a user using email or name
            $builder = $this->db->table('users');
            $user = $builder->where('email', $login)
                           ->orWhere('name', $login)
                           ->get()
                           ->getRowArray();

            // If user exists, verify the submitted password against the stored hash
            if ($user && password_verify($password, $user['password'])) {
                
                $sessionData = [
                    'userID'     => $user['id'],
                    'name'       => $user['name'],
                    'email'      => $user['email'],
                    'role'       => $user['role'],
                    'isLoggedIn' => true,
                    'loginTime'  => time() // Track login time for security
                ];

                $this->session->set($sessionData);
                $this->session->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');
                
                // Log successful login
                log_message('info', 'User login successful: ' . $user['email'] . ' from IP: ' . $this->request->getIPAddress());
                
                // Reset failed login attempts on successful login
                $this->session->remove('login_attempts');
                $this->session->remove('login_last_attempt');
                
                // Role-based redirection system
                switch ($user['role']) {
                    case 'admin':
                        return redirect()->to(base_url('admin/dashboard'));
                    case 'teacher':
                        return redirect()->to(base_url('teacher/dashboard'));
                    case 'student':
                        return redirect()->to(base_url('student/dashboard'));
                    default:
                        // Fallback for any unknown role
                        return redirect()->to(base_url('dashboard'));
                }
                
            } else {
                // Failed login attempt - log for security
                log_message('warning', 'Failed login attempt for: ' . $login . ' from IP: ' . $this->request->getIPAddress());
                
                // Track failed attempts
                $this->recordFailedAttempt();
                
                $this->session->setFlashdata('error', 'Invalid login credentials.');
            }
        }

        return view('auth/login');
    }

    /**
     * User logout - destroys session and redirects to login
     */
    public function logout()
    {
        $this->session->destroy();
        $this->session->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to(base_url('login'));
    }

    /**
     * Dashboard - redirects users to their role-based dashboard
     */
    public function dashboard()
    {
        if (!$this->isLoggedIn()) {
            $this->session->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to(base_url('login'));
        }

        // Redirect to role-based dashboard instead of showing generic dashboard
        $role = $this->session->get('role');
        switch ($role) {
            case 'admin':
                return redirect()->to(base_url('admin/dashboard'));
            case 'teacher':
                return redirect()->to(base_url('teacher/dashboard'));
            case 'student':
                return redirect()->to(base_url('student/dashboard'));
            default:
                // Fallback: if role is null or unknown, redirect to login
                $this->session->setFlashdata('error', 'Invalid user role. Please contact administrator.');
                return redirect()->to(base_url('login'));
        }
    }

    /**
     * Check if user is logged in
     */
    private function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') === true;
    }

    /**
     * Get current logged-in user data
     */
    public function getCurrentUser(): array
    {
        return [
            'userID' => $this->session->get('userID'),
            'name'   => $this->session->get('name'),
            'email'  => $this->session->get('email'),
            'role'   => $this->session->get('role')
        ];
    }

    /**
     * Check for excessive login attempts (rate limiting)
     */
    private function checkLoginAttempts(): bool
    {
        $attempts = $this->session->get('login_attempts') ?? 0;
        $lastAttempt = $this->session->get('login_last_attempt') ?? 0;
        
        // Block for 15 minutes after 5 failed attempts
        if ($attempts >= 5 && (time() - $lastAttempt) < 900) {
            $timeLeft = 900 - (time() - $lastAttempt);
            $minutes = ceil($timeLeft / 60);
            
            $this->session->setFlashdata('error', "Too many failed login attempts. Please try again in {$minutes} minutes.");
            return false; // Indicate that login should be blocked
        }
        
        // Reset attempts after 15 minutes
        if ((time() - $lastAttempt) > 900) {
            $this->session->remove('login_attempts');
            $this->session->remove('login_last_attempt');
        }
        
        return true; // Login attempts are within acceptable limits
    }

    /**
     * Record failed login attempt for rate limiting
     */
    private function recordFailedAttempt(): void
    {
        $attempts = $this->session->get('login_attempts') ?? 0;
        $this->session->set([
            'login_attempts' => $attempts + 1,
            'login_last_attempt' => time()
        ]);
    }
}