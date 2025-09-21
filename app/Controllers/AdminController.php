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
        
        return view('admin/dashboard', $data);
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

        $data = [
            'title' => 'User Management - Admin Panel',
            'users' => $users,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('admin/manage_users', $data);
    }
}