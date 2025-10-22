<?php

namespace App\Controllers;

/**
 * AdminController - Handles admin-specific functionality and dashboard
 */
class AdminController extends BaseController
{
    protected $session;
    protected $db;

    public function __construct()
    {
        // Initialize services for admin operations
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
    }

    /**
     * Admin Dashboard - displays admin-specific information and controls
     */
    public function dashboard()
    {
        // Authorization check - ensure user is logged in and has admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Gather statistics and data for admin dashboard
        $data = $this->prepareAdminDashboardData();
        
        return view('auth/dashboard', $data);
    }

    /**
     * Prepare data for admin dashboard display
     */
    private function prepareAdminDashboardData(): array
    {
        // Get total user counts by role
        $usersBuilder = $this->db->table('users');
        $totalUsers = $usersBuilder->countAllResults();
        
        $adminCount = $usersBuilder->where('role', 'admin')->countAllResults();
        $usersBuilder = $this->db->table('users'); // Reset builder
        $teacherCount = $usersBuilder->where('role', 'teacher')->countAllResults();
        $usersBuilder = $this->db->table('users'); // Reset builder
        $studentCount = $usersBuilder->where('role', 'student')->countAllResults();

        // Get recent user registrations (last 5 users)
        $usersBuilder = $this->db->table('users');
        $recentUsers = $usersBuilder->orderBy('created_at', 'DESC')
                                  ->limit(5)
                                  ->get()
                                  ->getResultArray();

        // For demo purposes - simulated course data
        $totalCourses = 15; // This would come from a courses table in a real app

        return [
            'title' => 'Admin Dashboard - ITE311 FUNDAR',
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'email'  => $this->session->get('email'),
                'role'   => $this->session->get('role')
            ],
            'stats' => [
                'totalUsers' => $totalUsers,
                'adminCount' => $adminCount,
                'teacherCount' => $teacherCount,
                'studentCount' => $studentCount,
                'totalCourses' => $totalCourses
            ],
            'recentUsers' => $recentUsers
        ];
    }

    /**
     * Check if user is logged in
     */
    private function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') === true;
    }

    /**
     * User Management - List all users (admin function)
     */
    public function manageUsers()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Get all users for management
        $usersBuilder = $this->db->table('users');
        $users = $usersBuilder->orderBy('created_at', 'DESC')->get()->getResultArray();

        // Get user statistics
        $stats = $this->getUserStats();

        $data = [
            'title' => 'User Management - Admin Panel',
            'users' => $users,
            'stats' => $stats,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ],
            // ADDED: Pass the current admin's ID to the view for comparison
            'currentUserId' => $this->session->get('userID')
        ];

        return view('admin/manage_users', $data);
    }

    /**
     * Get user statistics for dashboard display
     */
    private function getUserStats(): array
    {
        $usersBuilder = $this->db->table('users');
        $totalUsers = $usersBuilder->countAllResults();
        
        $usersBuilder = $this->db->table('users');
        $adminCount = $usersBuilder->where('role', 'admin')->countAllResults();
        
        $usersBuilder = $this->db->table('users');
        $teacherCount = $usersBuilder->where('role', 'teacher')->countAllResults();
        
        $usersBuilder = $this->db->table('users');
        $studentCount = $usersBuilder->where('role', 'student')->countAllResults();

        return [
            'totalUsers' => $totalUsers,
            'adminCount' => $adminCount,
            'teacherCount' => $teacherCount,
            'studentCount' => $studentCount
        ];
    }

    /**
     * Create new user (admin function)
     */
    public function createUser()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            // Validation rules with custom name validation
            $rules = [
                'name'     => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]*$/]',
                'email'    => 'required|valid_email|is_unique[users.email]|regex_match[/^[a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]*\.[a-zA-Z]{2,}$/]',
                'password' => 'required|min_length[6]',
                'role'     => 'required|in_list[admin,teacher,student]'
            ];

            // Custom validation messages
            $messages = [
                'name' => [
                    'required'     => 'Full name is required.',
                    'min_length'   => 'Name must be at least 3 characters long.',
                    'max_length'   => 'Name cannot exceed 100 characters.',
                    'regex_match'  => 'Name cannot contain quotes (\' \") or asterisks (*). Only letters, spaces, and Spanish characters allowed.'
                ],
                'email' => [
                    'required'    => 'Email address is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'is_unique'   => 'This email address is already registered.',
                    'regex_match' => 'Email cannot contain quotes (\' \") or asterisks (*). Only standard email characters allowed.'
                ]
            ];

            if ($this->validate($rules, $messages)) {
                // Hash password
                $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

                // Prepare user data
                $userData = [
                    'name'       => $this->request->getPost('name'),
                    'email'      => $this->request->getPost('email'),
                    'password'   => $hashedPassword,
                    'role'       => $this->request->getPost('role'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Insert user into database
                $usersBuilder = $this->db->table('users');
                if ($usersBuilder->insert($userData)) {
                    $this->session->setFlashdata('success', 'User created successfully!');
                } else {
                    $this->session->setFlashdata('error', 'Failed to create user. Please try again.');
                }
            } else {
                $this->session->setFlashdata('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
            }
        }

        return redirect()->to(base_url('admin/users'));
    }

    /**
     * Update user information (admin function)
     */
    public function updateUser()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $userId = $this->request->getPost('user_id');
            $currentAdminId = $this->session->get('userID');

            // FIXED: Prevent admin from editing their own account
            if ($userId == $currentAdminId) {
                $this->session->setFlashdata('error', 'You cannot edit your own account for security reasons.');
                return redirect()->to(base_url('admin/users'));
            }

            // Get current user data
            $usersBuilder = $this->db->table('users');
            $currentUser = $usersBuilder->where('id', $userId)->get()->getRowArray();

            if (!$currentUser) {
                $this->session->setFlashdata('error', 'User not found.');
                return redirect()->to(base_url('admin/users'));
            }

            // Validation rules - allows editing all roles (admin, teacher, student)
            $rules = [
                'name'  => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]*$/]',
                'email' => "required|valid_email|is_unique[users.email,id,{$userId}]|regex_match[/^[a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]*\.[a-zA-Z]{2,}$/]",
                'role'  => 'required|in_list[admin,teacher,student]'
            ];  

            // Custom validation messages
            $updateMessages = [
                'name' => [
                    'required'     => 'Full name is required.',
                    'min_length'   => 'Name must be at least 3 characters long.',
                    'max_length'   => 'Name cannot exceed 100 characters.',
                    'regex_match'  => 'Name cannot contain quotes (\' \") or asterisks (*). Only letters, spaces, and Spanish characters allowed.'
                ],
                'email' => [
                    'required'    => 'Email address is required.',
                    'valid_email' => 'Please enter a valid email address.',
                    'is_unique'   => 'This email address is already registered.',
                    'regex_match' => 'Email cannot contain quotes (\' \") or asterisks (*). Only standard email characters allowed.'
                ]
            ];

            if ($this->validate($rules, $updateMessages)) {
                // Prepare update data
                $updateData = [
                    'name'       => $this->request->getPost('name'),
                    'email'      => $this->request->getPost('email'),
                    'role'       => $this->request->getPost('role'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Update password if provided
                $newPassword = $this->request->getPost('password');
                if (!empty($newPassword)) {
                    if (strlen($newPassword) >= 6) {
                        $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                    } else {
                        $this->session->setFlashdata('error', 'Password must be at least 6 characters long.');
                        return redirect()->to(base_url('admin/users'));
                    }
                }

                // Update user in database
                $usersBuilder = $this->db->table('users');
                if ($usersBuilder->where('id', $userId)->update($updateData)) {
                    $this->session->setFlashdata('success', 'User updated successfully!');
                } else {
                    $this->session->setFlashdata('error', 'Failed to update user. Please try again.');
                }
            } else {
                $this->session->setFlashdata('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
            }
        }

        return redirect()->to(base_url('admin/users'));
    }

    /**
     * Delete user (admin function)
     */
    public function deleteUser()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $userId = $this->request->getPost('user_id');
            $currentAdminId = $this->session->get('userID');

            // Prevent deleting yourself
            if ($userId == $currentAdminId) {
                $this->session->setFlashdata('error', 'You cannot delete your own account for security reasons.');
                return redirect()->to(base_url('admin/users'));
            }

            // Get user data to check role
            $usersBuilder = $this->db->table('users');
            $user = $usersBuilder->where('id', $userId)->get()->getRowArray();

            if (!$user) {
                $this->session->setFlashdata('error', 'User not found.');
                return redirect()->to(base_url('admin/users'));
            }

            // UPDATED: Prevent deleting admin accounts
            if ($user['role'] === 'admin') {
                $this->session->setFlashdata('error', 'Admin accounts cannot be deleted for security reasons.');
                return redirect()->to(base_url('admin/users'));
            }

            // Delete user from database (only teachers and students)
            $usersBuilder = $this->db->table('users');
            if ($usersBuilder->where('id', $userId)->delete()) {
                $this->session->setFlashdata('success', 'User deleted successfully!');
            } else {
                $this->session->setFlashdata('error', 'Failed to delete user. Please try again.');
            }
        }
        return redirect()->to(base_url('admin/users'));
    }
}