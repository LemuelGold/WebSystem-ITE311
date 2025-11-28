<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        helper(['form']);

        if ($this->request->getMethod() === 'POST') {
            $session   = session();
            $userModel = new UserModel();

            $login = $this->request->getVar('login');
            $password = $this->request->getVar('password');

            $rules = [
                'login'    => 'required',
                'password' => 'required|min_length[6]'
            ];

            if (!$this->validate($rules)) {
                return view('auth/login', ['validation' => $this->validator]);
            }

            // Use the findByEmailOrUsername method to support both email and username
            $user = $userModel->findByEmailOrUsername($login);
            if ($user && password_verify($password, $user['password'])) {
                $session->set([
                    'userID'    => $user['id'],
                    'name'      => $user['name'],
                    'email'     => $user['email'],
                    'role'      => $user['role'],
                    'isLoggedIn'=> true
                ]);
                $session->setFlashdata('success', 'Welcome ' . $user['name']);
                return redirect()->to('/dashboard');
            }

            $session->setFlashdata('error', 'Invalid login credentials');
            return redirect()->back();
        }

        return view('auth/login');
    }

    public function register()
    {
        if ($this->request->getMethod() === 'post') {
            $name = trim($this->request->getPost('name'));
            $email = trim($this->request->getPost('email'));
            $username = trim($this->request->getPost('username'));
            $password = $this->request->getPost('password');
            $confirm_password = $this->request->getPost('confirm_password');

            // Validation
            if (empty($name) || empty($email) || empty($username) || empty($password)) {
                return redirect()->back()->with('error', 'Please fill in all fields');
            }

            // Email format validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('error', 'Please enter a valid email address');
            }

            // Password validation
            if (strlen($password) < 6) {
                return redirect()->back()->with('error', 'Password must be at least 6 characters long');
            }

            if ($password !== $confirm_password) {
                return redirect()->back()->with('error', 'Passwords do not match');
            }

            // Check if email already exists
            $existingUser = $this->userModel->where('email', $email)->first();
            if ($existingUser) {
                return redirect()->back()->with('error', 'Email already registered');
            }

            // Check if name already exists (as username)
            $existingName = $this->userModel->where('name', $name)->first();
            if ($existingName) {
                return redirect()->back()->with('error', 'Username already taken');
            }

            try {
                // Hash password and save user
                $data = [
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_BCRYPT),
                    'role' => 'user'
                ];

                if ($this->userModel->insert($data)) {
                    // Auto-login after registration
                    $user = $this->userModel->where('email', $email)->first();
                    $session = session();
                    $session->set([
                        'isLoggedIn' => true,
                        'userID' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]);
                    return redirect()->to('/dashboard')->with('success', 'Registration successful! Welcome to LMS!');
                }

                return redirect()->back()->with('error', 'Registration failed. Please try again.');
            } catch (\Exception $e) {
                log_message('error', 'Registration error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Registration failed. Please try again.');
            }
        }

        return view('auth/register');
    }

    public function dashboard()
    {
        $session = session();
    
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('login_error', 'Please log in first.');
        }
    
        $role = strtolower((string) $session->get('role'));
        $userId = (int) $session->get('userID');
        $userModel = new UserModel();
    
        // Get user profile
        $data = [
            'profile' => $userModel->find($userId),
            'name' => $session->get('name'),
            'email' => $session->get('email'),
            'role' => $role
        ];
    
        return view('auth/dashboard', [
            'role' => $role,
            'data' => $data,
        ]);
    }
    

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login')->with('success', 'Logged out successfully');
    }
}

