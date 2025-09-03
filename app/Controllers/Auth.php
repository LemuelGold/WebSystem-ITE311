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
        // If user is already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            return redirect()->to(base_url('dashboard'));
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
                    'role'       => 'user',
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
        // If user is already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            return redirect()->to(base_url('dashboard'));
        }

        // Check for a POST request
        if ($this->request->getMethod() === 'POST') {
            
            // Get the login field (can be email or username)
            $login = $this->request->getPost('login');
            $password = $this->request->getPost('password');

            // Basic validation
            if (empty($login) || empty($password)) {
                $this->session->setFlashdata('error', 'Please enter both login and password.');
                return view('auth/login');
            }

            // Check for hardcoded admin login
            if ($login === 'admin' && $password === 'admin123') {
                $sessionData = [
                    'userID'     => 1,
                    'name'       => 'Administrator',
                    'email'      => 'admin@lms.com',
                    'role'       => 'admin',
                    'isLoggedIn' => true
                ];
                $this->session->set($sessionData);
                $this->session->setFlashdata('success', 'Welcome back, Administrator!');
                return redirect()->to(base_url('dashboard'));
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
                    'isLoggedIn' => true
                ];

                $this->session->set($sessionData);
                $this->session->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');
                return redirect()->to(base_url('dashboard'));
                
            } else {
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
     * Dashboard - shows user dashboard after login
     */
    public function dashboard()
    {
        if (!$this->isLoggedIn()) {
            $this->session->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to(base_url('login'));
        }

        $userData = [
            'userID' => $this->session->get('userID'),
            'name'   => $this->session->get('name'),
            'email'  => $this->session->get('email'),
            'role'   => $this->session->get('role')
        ];
        
        $data = [
            'user' => $userData,
            'title' => 'LMS - Dashboard'
        ];

        return view('auth/dashboard', $data);
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
}