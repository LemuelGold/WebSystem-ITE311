<?php

namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
    }

    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        $role = strtolower($this->session->get('role') ?? '');
        if ($role !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Admin only.');
        }

        return null;
    }

    /**
     * List all users
     */
    public function users()
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        // Include deleted users (soft deletes) in the list
        $users = $this->userModel->withDeleted()->orderBy('created_at', 'DESC')->findAll();
        
        $data = [
            'title' => 'User Management',
            'users' => $users,
            'name' => $this->session->get('name'),
            'email' => $this->session->get('email'),
            'role' => $this->session->get('role')
        ];

        return view('admin/users/index', $data);
    }

    /**
     * Show add user form
     */
    public function addUser()
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        $data = [
            'title' => 'Add New User',
            'name' => $this->session->get('name'),
            'email' => $this->session->get('email'),
            'role' => $this->session->get('role'),
            'user' => null
        ];

        return view('admin/users/form', $data);
    }

    /**
     * Process add user
     */
    public function storeUser()
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        helper(['form']);

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[A-Za-z0-9\s]+$/]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[admin,teacher,student]'
        ];

        $messages = [
            'name' => [
                'regex_match' => 'Name can only contain letters, numbers, and spaces. Special characters are not allowed.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            $data = [
                'title' => 'Add New User',
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'role' => $this->session->get('role'),
                'user' => [
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'role' => $this->request->getPost('role')
                ],
                'validation' => $this->validator
            ];
            return view('admin/users/form', $data);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role' => $this->request->getPost('role')
        ];

        if ($this->userModel->insert($data)) {
            return redirect()->to('/admin/users')->with('success', 'User added successfully!');
        }

        return redirect()->back()->with('error', 'Failed to add user. Please try again.');
    }

    /**
     * Show edit user form
     */
    public function editUser($id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        // Include deleted users so they can be edited/restored
        $user = $this->userModel->withDeleted()->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'name' => $this->session->get('name'),
            'email' => $this->session->get('email'),
            'role' => $this->session->get('role')
        ];

        return view('admin/users/form', $data);
    }

    /**
     * Process update user
     */
    public function updateUser($id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        helper(['form']);

        // Include deleted users so they can be updated/restored
        $user = $this->userModel->withDeleted()->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        $isDeleted = !empty($user['deleted_at']);

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[A-Za-z0-9\s]+$/]',
            'email' => 'required|valid_email',
            'role' => 'required|in_list[admin,teacher,student]'
        ];

        $messages = [
            'name' => [
                'regex_match' => 'Name can only contain letters, numbers, and spaces. Special characters are not allowed.'
            ]
        ];

        $email = $this->request->getPost('email');
        $existingUser = $this->userModel->where('email', $email)->where('id !=', $id)->first();
        if ($existingUser) {
            $rules['email'] = 'required|valid_email|is_unique[users.email]';
        }

        // If user is admin, lock the role - use the existing role
        if (strtolower($user['role']) === 'admin') {
            // Force role to remain admin
            $_POST['role'] = 'admin';
        }

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules, $messages)) {
            $data = [
                'title' => 'Edit User',
                'name' => $this->session->get('name'),
                'email' => $this->session->get('email'),
                'role' => $this->session->get('role'),
                'user' => [
                    'id' => $id,
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'role' => $this->request->getPost('role')
                ],
                'validation' => $this->validator
            ];
            return view('admin/users/form', $data);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role')
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        // If user is deleted, restore them by clearing deleted_at
        if ($isDeleted) {
            $data['deleted_at'] = null;
        }

        // Use withDeleted() to update even deleted users
        if ($this->userModel->withDeleted()->update($id, $data)) {
            $message = $isDeleted ? 'User restored and updated successfully!' : 'User updated successfully!';
            return redirect()->to('/admin/users')->with('success', $message);
        }

        return redirect()->back()->with('error', 'Failed to update user. Please try again.');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $check = $this->checkAdmin();
        if ($check) return $check;

        if ($id == $this->session->get('userID')) {
            return redirect()->to('/admin/users')->with('error', 'You cannot delete your own account.');
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('success', 'User has been marked as deleted successfully!');
        }

        return redirect()->to('/admin/users')->with('error', 'Failed to delete user. Please try again.');
    }
}

