<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function register()
    {
        helper(['form']);
        $data = [];

        if ($this->request->getMethod() == 'post') {
            // Define validation rules for registration
            $rules = [
                'username'         => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                'first_name'       => 'required|min_length[2]|max_length[50]',
                'last_name'        => 'required|min_length[2]|max_length[50]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[6]|max_length[255]',
                'password_confirm' => 'matches[password]',
            ];

            if ($this->validate($rules)) {
                // Save user to database
                $model = new UserModel();
                $userData = [
                    'username'   => $this->request->getPost('username'),
                    'email'      => $this->request->getPost('email'),
                    'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT), // Hash password
                    'first_name' => $this->request->getPost('first_name'),
                    'last_name'  => $this->request->getPost('last_name'),
                    'role'       => 'student', // Default role
                    'status'     => 'active',
                ];

                $model->save($userData);
                session()->setFlashdata('success', 'Registration successful! Please login.');
                return redirect()->to('/login');
            } else {
                // Pass validation errors to view
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/register', $data);
    }

    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            $login = $this->request->getPost('login');
            $password = $this->request->getPost('password');
            
            if ($login === 'admin' && $password === '12345678') {
                session()->set([
                    'isLoggedIn' => true,
                    'username' => 'admin',
                    'userID' => 1,
                    'name' => 'Administrator',
                    'email' => 'admin@lms.com',
                    'role' => 'admin'
                ]);
                
                return redirect()->to(base_url('dashboard'));
            } else {
                session()->setFlashdata('error', 'Invalid login credentials.');
                return redirect()->to(base_url('login'));
            }
        }

        return view('auth/login');
    }
    
    public function logout()
    {
        // Use CodeIgniter session to destroy
        session()->destroy();
        
        // Redirect to login
        return redirect()->to(base_url('login'));
    }

    public function dashboard()
    {
        return view('auth/dashboard');
    }
}