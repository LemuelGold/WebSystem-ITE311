<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\NotificationModel;
use App\Models\ProgramModel;
use App\Models\StudentProgramModel;

/**
 * AdminController - Handles admin-specific functionality and dashboard
 */
class AdminController extends BaseController
{
    protected $session;
    protected $db;
    protected $courseModel;
    protected $notificationModel;
    protected $programModel;
    protected $studentProgramModel;

    public function __construct()
    {
        // Initialize services for admin operations
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->courseModel = new CourseModel();
        $this->notificationModel = new NotificationModel();
        $this->programModel = new ProgramModel();
        $this->studentProgramModel = new StudentProgramModel();
    }

    /**
     * Admin Dashboard -- displays admin-specific information and controls
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
        // Get total user counts by role (exclude soft-deleted)
        $usersBuilder = $this->db->table('users');
        $totalUsers = $usersBuilder->where('deleted_at IS NULL')->countAllResults();
        
        $usersBuilder = $this->db->table('users'); // Reset builder
        $adminCount = $usersBuilder->where('role', 'admin')
                                  ->where('deleted_at IS NULL')
                                  ->countAllResults();
        $usersBuilder = $this->db->table('users'); // Reset builder
        $teacherCount = $usersBuilder->where('role', 'teacher')
                                    ->where('deleted_at IS NULL')
                                    ->countAllResults();
        $usersBuilder = $this->db->table('users'); // Reset builder
        $studentCount = $usersBuilder->where('role', 'student')
                                    ->where('deleted_at IS NULL')
                                    ->countAllResults();

        // Get recent user registrations (last 5 active users)
        $usersBuilder = $this->db->table('users');
        $recentUsers = $usersBuilder->where('deleted_at IS NULL')
                                  ->orderBy('created_at', 'DESC')
                                  ->limit(5)
                                  ->get()
                                  ->getResultArray();

        // For demo purposes - simulated course data
        $totalCourses = 15; // This would come from a courses table in a real app

        return [
            'title' => 'Admin Dashboard - RESTAURO LMS',
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
     * Check if user is logged in and session is valid
     */
    private function isLoggedIn(): bool
    {
        if ($this->session->get('isLoggedIn') !== true) {
            return false;
        }
        
        // Check if session token is still valid (for auto-logout on profile changes)
        $userId = $this->session->get('userID');
        $sessionToken = $this->session->get('sessionToken');
        
        if ($userId) {
            $usersBuilder = $this->db->table('users');
            $user = $usersBuilder->where('id', $userId)->get()->getRowArray();
            
            // If user doesn't exist or is soft deleted, invalidate session
            if (!$user || !empty($user['deleted_at'])) {
                $this->session->destroy();
                return false;
            }
            
            // If session token exists in database but doesn't match current session, user was logged out
            if (!empty($user['session_token']) && $user['session_token'] !== $sessionToken) {
                $this->session->destroy();
                return false;
            }
        }
        
        return true;
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

        // Get all active users for management (exclude soft-deleted)
        $usersBuilder = $this->db->table('users');
        $users = $usersBuilder->where('deleted_at IS NULL')
                             ->orderBy('created_at', 'DESC')
                             ->get()
                             ->getResultArray();

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
     * Get user statistics for dashboard display (excludes soft-deleted users)
     */
    private function getUserStats(): array
    {
        $usersBuilder = $this->db->table('users');
        $totalUsers = $usersBuilder->where('deleted_at IS NULL')->countAllResults();
        
        $usersBuilder = $this->db->table('users');
        $adminCount = $usersBuilder->where('role', 'admin')
                                  ->where('deleted_at IS NULL')
                                  ->countAllResults();
        
        $usersBuilder = $this->db->table('users');
        $teacherCount = $usersBuilder->where('role', 'teacher')
                                    ->where('deleted_at IS NULL')
                                    ->countAllResults();
        
        $usersBuilder = $this->db->table('users');
        $studentCount = $usersBuilder->where('role', 'student')
                                    ->where('deleted_at IS NULL')
                                    ->countAllResults();

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
                // Check for sensitive data changes that require logout
                $sensitiveDataChanged = false;
                $changedFields = [];
                
                // Check if name changed
                if ($currentUser['name'] !== $this->request->getPost('name')) {
                    $sensitiveDataChanged = true;
                    $changedFields[] = 'name';
                }
                
                // Check if email changed
                if ($currentUser['email'] !== $this->request->getPost('email')) {
                    $sensitiveDataChanged = true;
                    $changedFields[] = 'email';
                }
                
                // Check if password is being changed
                $newPassword = $this->request->getPost('password');
                if (!empty($newPassword)) {
                    $sensitiveDataChanged = true;
                    $changedFields[] = 'password';
                }

                // Prepare update data
                $updateData = [
                    'name'       => $this->request->getPost('name'),
                    'email'      => $this->request->getPost('email'),
                    'role'       => $this->request->getPost('role'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Update password if provided
                if (!empty($newPassword)) {
                    if (strlen($newPassword) >= 6) {
                        $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                    } else {
                        $this->session->setFlashdata('error', 'Password must be at least 6 characters long.');
                        return redirect()->to(base_url('admin/users'));
                    }
                }

                // If sensitive data changed, invalidate user's session by updating their session token
                if ($sensitiveDataChanged) {
                    // Add a session_token field to force logout
                    $updateData['session_token'] = bin2hex(random_bytes(32));
                }

                // Update user in database
                $usersBuilder = $this->db->table('users');
                if ($usersBuilder->where('id', $userId)->update($updateData)) {
                    if ($sensitiveDataChanged) {
                        // Create notification for the affected user
                        $changedFieldsText = implode(', ', $changedFields);
                        $notificationMessage = "Your account information ({$changedFieldsText}) has been updated by an administrator. Please log in again for security.";
                        $this->notificationModel->createNotification($userId, $notificationMessage);
                        
                        $this->session->setFlashdata('success', "User updated successfully! The user has been automatically logged out due to sensitive data changes ({$changedFieldsText}) and will need to log in again.");
                    } else {
                        $this->session->setFlashdata('success', 'User updated successfully!');
                    }
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
     * Delete user (admin function) - Uses soft delete
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

            // Get user data to check role and if already deleted
            $usersBuilder = $this->db->table('users');
            $user = $usersBuilder->where('id', $userId)
                                ->where('deleted_at IS NULL')
                                ->get()
                                ->getRowArray();

            if (!$user) {
                $this->session->setFlashdata('error', 'User not found or already deleted.');
                return redirect()->to(base_url('admin/users'));
            }

            // UPDATED: Prevent deleting admin accounts
            if ($user['role'] === 'admin') {
                $this->session->setFlashdata('error', 'Admin accounts cannot be deleted for security reasons.');
                return redirect()->to(base_url('admin/users'));
            }

            // Soft delete user - set deleted_at timestamp instead of removing record
            $usersBuilder = $this->db->table('users');
            $softDeleteData = [
                'deleted_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if ($usersBuilder->where('id', $userId)->update($softDeleteData)) {
                $this->session->setFlashdata('success', 'User deleted successfully! (User data preserved for recovery)');
            } else {
                $this->session->setFlashdata('error', 'Failed to delete user. Please try again.');
            }
        }
        return redirect()->to(base_url('admin/users'));
    }

    /**
     * Course Management - List all courses (admin function)
     */
    public function manageCourses()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Fix existing courses with invalid created_at timestamps (one-time fix)
        $this->db->table('courses')
            ->where('created_at IS NULL OR created_at = "0000-00-00 00:00:00" OR created_at < "2020-01-01"')
            ->update([
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // Get courses grouped by title (show only one row per course title)
        $coursesBuilder = $this->db->table('courses');
        $allCourses = $coursesBuilder
            ->select('courses.*, users.name as instructor_name, users.email as instructor_email, 
                     COALESCE(enrollment_counts.enrolled_count, 0) as enrolled_count')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->join('(SELECT course_id, COUNT(*) as enrolled_count FROM enrollments WHERE status = "confirmed" GROUP BY course_id) as enrollment_counts', 
                   'enrollment_counts.course_id = courses.id', 'left')
            ->orderBy('courses.title', 'ASC')
            ->orderBy('courses.section', 'ASC')
            ->orderBy('courses.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Group courses by title - show only one row per course title
        $courses = [];
        $courseGroups = [];
        
        foreach ($allCourses as $course) {
            $title = $course['title'];
            
            if (!isset($courseGroups[$title])) {
                // First occurrence of this course title - use it as the main entry
                $courseGroups[$title] = $course;
                $courseGroups[$title]['section_count'] = 1;
                $courseGroups[$title]['total_enrolled'] = (int)$course['enrolled_count'];
            } else {
                // Additional section of the same course - update counts
                $courseGroups[$title]['section_count']++;
                $courseGroups[$title]['total_enrolled'] += (int)$course['enrolled_count'];
            }
        }
        
        // Convert back to indexed array
        $courses = array_values($courseGroups);
        
        // Get all active teachers for the dropdown (exclude soft-deleted)
        $teachersBuilder = $this->db->table('users');
        $teachers = $teachersBuilder->where('role', 'teacher')
            ->where('deleted_at IS NULL')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Course Management - Admin Panel',
            'courses' => $courses,
            'teachers' => $teachers,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('admin/manage_courses', $data);
    }

    /**
     * Create new course (admin function)
     */
    public function createCourse()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            // Custom validation: If semester is selected, academic year is required
            $semester = $this->request->getPost('semester');
            $academicYear = $this->request->getPost('academic_year');
            
            if (!empty($semester) && empty($academicYear)) {
                $this->session->setFlashdata('error', 'Academic Year is required when Semester is selected.');
                return redirect()->to(base_url('admin/courses'));
            }

            // Validation rules - Allow duplicate course IDs for multiple sections
            $rules = [
                'course_id'     => 'required|exact_length[4]|numeric',
                'title'         => 'required|min_length[3]|max_length[255]',
                'description'   => 'permit_empty|max_length[1000]',
                'units'         => 'permit_empty|integer|greater_than[0]|less_than[10]',
                'term'          => 'permit_empty|in_list[Term 1,Term 2]',
                'instructor_id' => 'permit_empty|integer',
                'section'       => 'permit_empty|max_length[10]',
                'schedule_time' => 'permit_empty|max_length[50]',
                'room'          => 'permit_empty|max_length[50]',
                'start_date'    => 'permit_empty|valid_date',
                'end_date'      => 'permit_empty|valid_date',
                'academic_year' => 'permit_empty|max_length[20]',
                'semester'      => 'permit_empty|in_list[1st Semester,2nd Semester]',
                'status'        => 'required|in_list[active,inactive]'
            ];

            // Custom validation messages
            $messages = [
                'course_id' => [
                    'required'     => 'Course ID is required.',
                    'exact_length' => 'Course ID must be exactly 4 digits.',
                    'numeric'      => 'Course ID must contain only numbers.'
                ],
                'title' => [
                    'required'     => 'Course title is required.',
                    'min_length'   => 'Course title must be at least 3 characters long.',
                    'max_length'   => 'Course title cannot exceed 255 characters.'
                ],
                'units' => [
                    'integer'      => 'Units must be a number.',
                    'greater_than' => 'Units must be greater than 0.',
                    'less_than'    => 'Units must be less than 10.'
                ],
                'term' => [
                    'in_list' => 'Invalid term selection.'
                ],
                'instructor_id' => [
                    'integer'  => 'Invalid instructor selection.'
                ],
                'start_date' => [
                    'valid_date' => 'Start date must be a valid date.'
                ],
                'end_date' => [
                    'valid_date' => 'End date must be a valid date.'
                ],
                'status' => [
                    'required' => 'Course status is required.',
                    'in_list'  => 'Invalid status value.'
                ]
            ];

            if ($this->validate($rules, $messages)) {
                // Verify instructor exists and is a teacher (only if instructor is selected)
                $instructorId = $this->request->getPost('instructor_id');
                if (!empty($instructorId)) {
                    $usersBuilder = $this->db->table('users');
                    $instructor = $usersBuilder->where('id', $instructorId)
                        ->where('role', 'teacher')
                        ->where('deleted_at IS NULL')
                        ->get()
                        ->getRowArray();

                    if (!$instructor) {
                        $this->session->setFlashdata('error', 'Invalid instructor selected. Only active teachers can be assigned to courses.');
                        return redirect()->to(base_url('admin/courses'));
                    }

                    // Optional: Check for schedule conflicts (if schedule_time is provided)
                    $scheduleTime = $this->request->getPost('schedule_time');
                    if (!empty($scheduleTime)) {
                        $conflictingCourses = $this->db->table('courses')
                            ->where('instructor_id', $instructorId)
                            ->where('schedule_time', $scheduleTime)
                            ->where('status', 'active')
                            ->countAllResults();

                        if ($conflictingCourses > 0) {
                            $this->session->setFlashdata('error', 'This instructor already has a course scheduled at this time. Please choose a different schedule.');
                            return redirect()->to(base_url('admin/courses'));
                        }
                    }
                } else {
                    $instructorId = null; // Set to null for unassigned courses
                }

                // Prepare course data
                $courseData = [
                    'course_code'   => $this->request->getPost('course_id'),
                    'title'         => $this->request->getPost('title'),
                    'description'   => $this->request->getPost('description'),
                    'units'         => $this->request->getPost('units') ?: null,
                    'term'          => $this->request->getPost('term') ?: null,
                    'instructor_id' => !empty($instructorId) ? $instructorId : null,
                    'section'       => $this->request->getPost('section') ?: null,
                    'schedule_time' => $this->request->getPost('schedule_time') ?: null,
                    'room'          => $this->request->getPost('room') ?: null,
                    'start_date'    => $this->request->getPost('start_date') ?: null,
                    'end_date'      => $this->request->getPost('end_date') ?: null,
                    'academic_year' => $this->request->getPost('academic_year') ?: null,
                    'semester'      => $this->request->getPost('semester') ?: null,
                    'status'        => $this->request->getPost('status'),
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s')
                ];

                // Insert course into database
                $coursesBuilder = $this->db->table('courses');
                if ($coursesBuilder->insert($courseData)) {
                    $newCourseId = $this->db->insertID();

                    $courseTitle = $this->request->getPost('title');
                    $adminName = $this->session->get('name');
                    
                    // Send notification to teacher if instructor is assigned
                    if (!empty($instructorId)) {
                        $notificationMessage = "You have been assigned as instructor for the course: {$courseTitle} by {$adminName}.";
                        $this->notificationModel->createNotification($instructorId, $notificationMessage);
                    }
                    
                    // Check if this is adding a section (from the "Add Section" button)
                    $isAddingSection = $this->request->getPost('is_adding_section');
                    

                    
                    if ($isAddingSection) {
                        $sectionName = $this->request->getPost('section');
                        $this->session->setFlashdata('success', "New section '{$sectionName}' created successfully! You are now viewing the new section's student management page.");
                    } else {
                        $this->session->setFlashdata('success', 'Course created successfully! You can now invite students to this course.');
                    }
                    

                    // If adding a section, redirect back to the new section to show it in the tabs
                    if ($isAddingSection) {
                        return redirect()->to(base_url("admin/course/{$newCourseId}/students"));
                    } else {
                        // For new courses, redirect to the course students page
                        return redirect()->to(base_url("admin/course/{$newCourseId}/students"));
                    }
                } else {
                    $this->session->setFlashdata('error', 'Failed to create course. Please try again.');
                }
            } else {
                $this->session->setFlashdata('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
            }
        }

        return redirect()->to(base_url('admin/courses'));
    }

    /**
     * Update course information (admin function)
     */
    public function updateCourse()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $courseId = $this->request->getPost('course_id');

            // Get current course data
            $coursesBuilder = $this->db->table('courses');
            $currentCourse = $coursesBuilder->where('id', $courseId)->get()->getRowArray();

            if (!$currentCourse) {
                $this->session->setFlashdata('error', 'Course not found.');
                return redirect()->to(base_url('admin/courses'));
            }

            // Custom validation: If semester is selected, academic year is required
            $semester = $this->request->getPost('semester');
            $academicYear = $this->request->getPost('academic_year');
            
            if (!empty($semester) && empty($academicYear)) {
                $this->session->setFlashdata('error', 'Academic Year is required when Semester is selected.');
                return redirect()->to(base_url('admin/courses'));
            }

            // Validation rules
            $rules = [
                'title'         => 'required|min_length[3]|max_length[255]',
                'description'   => 'permit_empty|max_length[1000]',
                'units'         => 'permit_empty|integer|greater_than[0]|less_than[10]',
                'term'          => 'permit_empty|in_list[Term 1,Term 2]',
                'instructor_id' => 'permit_empty|integer',
                'section'       => 'permit_empty|max_length[10]',
                'schedule_time' => 'permit_empty|max_length[50]',
                'room'          => 'permit_empty|max_length[50]',
                'start_date'    => 'permit_empty|valid_date',
                'end_date'      => 'permit_empty|valid_date',
                'academic_year' => 'permit_empty|max_length[20]',
                'semester'      => 'permit_empty|in_list[1st Semester,2nd Semester]',
                'status'        => 'required|in_list[active,inactive]'
            ];

            // Custom validation messages
            $messages = [
                'title' => [
                    'required'     => 'Course title is required.',
                    'min_length'   => 'Course title must be at least 3 characters long.',
                    'max_length'   => 'Course title cannot exceed 255 characters.'
                ],
                'units' => [
                    'integer'      => 'Units must be a number.',
                    'greater_than' => 'Units must be greater than 0.',
                    'less_than'    => 'Units must be less than 10.'
                ],
                'term' => [
                    'in_list' => 'Invalid term selection.'
                ],
                'instructor_id' => [
                    'integer'  => 'Invalid instructor selection.'
                ],
                'start_date' => [
                    'valid_date' => 'Start date must be a valid date.'
                ],
                'end_date' => [
                    'valid_date' => 'End date must be a valid date.'
                ],
                'status' => [
                    'required' => 'Course status is required.',
                    'in_list'  => 'Invalid status value.'
                ]
            ];

            if ($this->validate($rules, $messages)) {
                // Verify instructor exists and is a teacher (only if instructor is selected)
                $instructorId = $this->request->getPost('instructor_id');
                if (!empty($instructorId)) {
                    $usersBuilder = $this->db->table('users');
                    $instructor = $usersBuilder->where('id', $instructorId)
                        ->where('role', 'teacher')
                        ->where('deleted_at IS NULL')
                        ->get()
                        ->getRowArray();

                    if (!$instructor) {
                        $this->session->setFlashdata('error', 'Invalid instructor selected. Only active teachers can be assigned to courses.');
                        return redirect()->to(base_url('admin/courses'));
                    }

                    // Optional: Check for schedule conflicts when changing instructor or schedule
                    $newScheduleTime = $this->request->getPost('schedule_time');
                    if (!empty($newScheduleTime) && ($instructorId != $currentCourse['instructor_id'] || $newScheduleTime != $currentCourse['schedule_time'])) {
                        $conflictingCourses = $this->db->table('courses')
                            ->where('instructor_id', $instructorId)
                            ->where('schedule_time', $newScheduleTime)
                            ->where('status', 'active')
                            ->where('id !=', $courseId) // Exclude current course
                            ->countAllResults();

                        if ($conflictingCourses > 0) {
                            $this->session->setFlashdata('error', 'This instructor already has a course scheduled at this time. Please choose a different schedule.');
                            return redirect()->to(base_url('admin/courses'));
                        }
                    }
                } else {
                    $instructorId = null; // Set to null for unassigned courses
                }

                // Check if Course ID is being changed
                $newCourseId = $this->request->getPost('new_course_id');
                $courseIdChanged = false;
                
                // Only consider it a change if new_course_id is provided AND different from current course ID
                if (!empty($newCourseId) && is_numeric($newCourseId) && (int)$newCourseId != (int)$courseId) {
                    // Validate new Course ID
                    if (!is_numeric($newCourseId) || $newCourseId <= 0) {
                        $this->session->setFlashdata('error', 'Course ID must be a positive number.');
                        return redirect()->to(base_url('admin/courses'));
                    }
                    
                    // Convert to integer to check actual database value
                    $newCourseIdInt = (int)$newCourseId;
                    
                    // Check if new Course ID already exists
                    $existingCourse = $this->db->table('courses')->where('id', $newCourseIdInt)->get()->getRowArray();
                    if ($existingCourse) {
                        $this->session->setFlashdata('error', 'Course ID ' . $newCourseIdInt . ' already exists. Please choose a different ID.');
                        return redirect()->to(base_url('admin/courses'));
                    }
                    
                    // Use the integer value for the new ID
                    $newCourseId = $newCourseIdInt;
                    
                    $courseIdChanged = true;
                }

                // Prepare update data
                $updateData = [
                    'title'         => $this->request->getPost('title'),
                    'description'   => $this->request->getPost('description'),
                    'units'         => $this->request->getPost('units') ?: null,
                    'term'          => $this->request->getPost('term') ?: null,
                    'instructor_id' => !empty($instructorId) ? $instructorId : null,
                    'section'       => $this->request->getPost('section') ?: null,
                    'schedule_time' => $this->request->getPost('schedule_time') ?: null,
                    'room'          => $this->request->getPost('room') ?: null,
                    'start_date'    => $this->request->getPost('start_date') ?: null,
                    'end_date'      => $this->request->getPost('end_date') ?: null,
                    'academic_year' => $this->request->getPost('academic_year') ?: null,
                    'semester'      => $this->request->getPost('semester') ?: null,
                    'status'        => $this->request->getPost('status'),
                    'updated_at'    => date('Y-m-d H:i:s')
                ];

                // Ensure created_at is preserved or set to current time if invalid
                if (empty($currentCourse['created_at']) || 
                    $currentCourse['created_at'] === '0000-00-00 00:00:00' || 
                    strtotime($currentCourse['created_at']) < strtotime('2020-01-01')) {
                    $updateData['created_at'] = date('Y-m-d H:i:s');
                }

                // If Course ID is being changed, we need to handle it carefully
                if ($courseIdChanged) {
                    // Start transaction for data integrity
                    $this->db->transStart();
                    
                    try {
                        // Add new Course ID to update data
                        $updateData['id'] = $newCourseId;
                        $updateData['course_code'] = $newCourseId; // Set course_code to match the new ID
                        
                        // Insert new record with new ID
                        $coursesBuilder = $this->db->table('courses');
                        $coursesBuilder->insert($updateData);
                        
                        // Update related tables (enrollments, materials, etc.)
                        $this->db->table('enrollments')->where('course_id', $courseId)->update(['course_id' => $newCourseId]);
                        $this->db->table('materials')->where('course_id', $courseId)->update(['course_id' => $newCourseId]);
                        
                        // Delete old record
                        $this->db->table('courses')->where('id', $courseId)->delete();
                        
                        $this->db->transComplete();
                        
                        if ($this->db->transStatus() === FALSE) {
                            $this->session->setFlashdata('error', 'Failed to update Course ID. Database transaction failed.');
                        } else {
                            // Send notification to teacher if instructor was changed
                            if ($currentCourse['instructor_id'] != $instructorId) {
                                $courseTitle = $this->request->getPost('title');
                                $adminName = $this->session->get('name');
                                
                                // If new instructor is assigned
                                if (!empty($instructorId)) {
                                    $notificationMessage = "You have been assigned as instructor for the course: {$courseTitle} by {$adminName}.";
                                    $this->notificationModel->createNotification($instructorId, $notificationMessage);
                                }
                                
                                // If previous instructor is removed (set to unassigned)
                                if (!empty($currentCourse['instructor_id']) && empty($instructorId)) {
                                    $notificationMessage = "You have been removed as instructor from the course: {$courseTitle} by {$adminName}.";
                                    $this->notificationModel->createNotification($currentCourse['instructor_id'], $notificationMessage);
                                }
                            }
                            
                            $this->session->setFlashdata('success', 'Course updated successfully! Course ID changed from ' . $courseId . ' to ' . $newCourseId . '.');
                        }
                    } catch (\Exception $e) {
                        $this->db->transRollback();
                        $this->session->setFlashdata('error', 'Failed to update Course ID: ' . $e->getMessage());
                    }
                } else {
                    // Normal update without ID change
                    $coursesBuilder = $this->db->table('courses');
                    if ($coursesBuilder->where('id', $courseId)->update($updateData)) {
                        // Check if instructor was changed and send notification
                        if ($currentCourse['instructor_id'] != $instructorId) {
                            $courseTitle = $this->request->getPost('title');
                            $adminName = $this->session->get('name');
                            
                            // If new instructor is assigned
                            if (!empty($instructorId)) {
                                $notificationMessage = "You have been assigned as instructor for the course: {$courseTitle} by {$adminName}.";
                                $this->notificationModel->createNotification($instructorId, $notificationMessage);
                            }
                            
                            // If previous instructor is removed (set to unassigned)
                            if (!empty($currentCourse['instructor_id']) && empty($instructorId)) {
                                $notificationMessage = "You have been removed as instructor from the course: {$courseTitle} by {$adminName}.";
                                $this->notificationModel->createNotification($currentCourse['instructor_id'], $notificationMessage);
                            }
                        }
                        
                        $this->session->setFlashdata('success', 'Course updated successfully!');
                    } else {
                        $this->session->setFlashdata('error', 'Failed to update course. Please try again.');
                    }
                }
            } else {
                $this->session->setFlashdata('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
            }
        }

        return redirect()->to(base_url('admin/courses'));
    }

    /**
     * Delete course (admin function)
     */
    public function deleteCourse()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $courseId = $this->request->getPost('course_id');

            // Get course data
            $coursesBuilder = $this->db->table('courses');
            $course = $coursesBuilder->where('id', $courseId)->get()->getRowArray();

            if (!$course) {
                $this->session->setFlashdata('error', 'Course not found.');
                return redirect()->to(base_url('admin/courses'));
            }

            // Check if course has enrolled students
            $enrollmentsBuilder = $this->db->table('enrollments');
            $enrollmentCount = $enrollmentsBuilder->where('course_id', $courseId)->countAllResults();

            if ($enrollmentCount > 0) {
                $this->session->setFlashdata('error', "Cannot delete course. It has {$enrollmentCount} enrolled student(s). Please remove all students first.");
                return redirect()->to(base_url('admin/courses'));
            }

            // Delete course from database
            $coursesBuilder = $this->db->table('courses');
            if ($coursesBuilder->where('id', $courseId)->delete()) {
                $this->session->setFlashdata('success', 'Course deleted successfully!');
            } else {
                $this->session->setFlashdata('error', 'Failed to delete course. Please try again.');
            }
        }

        return redirect()->to(base_url('admin/courses'));
    }

    /**
     * View enrolled students for a specific course (admin function)
     */
    public function viewCourseStudents($courseId)
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Get course information
        $coursesBuilder = $this->db->table('courses');
        $course = $coursesBuilder
            ->select('courses.*, users.name as instructor_name, users.email as instructor_email')
            ->join('users', 'users.id = courses.instructor_id', 'left')
            ->where('courses.id', $courseId)
            ->get()
            ->getRowArray();

        if (!$course) {
            $this->session->setFlashdata('error', 'Course not found.');
            return redirect()->to(base_url('admin/courses'));
        }

        // Get enrolled students with enrollment details
        $enrollmentsBuilder = $this->db->table('enrollments');
        $enrolledStudents = $enrollmentsBuilder
            ->select('enrollments.*, users.name as student_name, users.email as student_email, users.id as student_id')
            ->join('users', 'users.id = enrollments.user_id', 'inner')
            ->where('enrollments.course_id', $courseId)
            ->where('enrollments.status', 'confirmed')
            ->where('users.deleted_at IS NULL')
            ->orderBy('users.name', 'ASC')
            ->get()
            ->getResultArray();

        // Get pending enrollment requests with student details
        $pendingEnrollmentsBuilder = $this->db->table('enrollments');
        $pendingEnrollmentsList = $pendingEnrollmentsBuilder
            ->select('enrollments.*, users.name as student_name, users.email as student_email, users.id as student_id')
            ->join('users', 'users.id = enrollments.user_id', 'inner')
            ->where('enrollments.course_id', $courseId)
            ->where('enrollments.status', 'pending')
            ->where('users.deleted_at IS NULL')
            ->orderBy('enrollments.created_at', 'ASC')
            ->get()
            ->getResultArray();

        // Get all sections of the same course (same title)
        $allSectionsBuilder = $this->db->table('courses');
        $allSections = $allSectionsBuilder
            ->select('courses.id, courses.title, courses.section, courses.schedule_time, courses.room, 
                     COALESCE(enrollment_counts.enrolled_count, 0) as enrolled_count,
                     COALESCE(pending_counts.pending_count, 0) as pending_count')
            ->join('(SELECT course_id, COUNT(*) as enrolled_count FROM enrollments WHERE status = "confirmed" GROUP BY course_id) as enrollment_counts', 
                   'enrollment_counts.course_id = courses.id', 'left')
            ->join('(SELECT course_id, COUNT(*) as pending_count FROM enrollments WHERE status = "pending" GROUP BY course_id) as pending_counts', 
                   'pending_counts.course_id = courses.id', 'left')
            ->where('courses.title', $course['title'])
            ->orderBy('courses.section', 'ASC')
            ->get()
            ->getResultArray();

        // Get enrollment statistics
        $totalEnrolled = count($enrolledStudents);
        $pendingEnrollments = count($pendingEnrollmentsList);

        $data = [
            'title' => 'Course Students - ' . $course['title'],
            'course' => $course,
            'allSections' => $allSections,
            'currentCourseId' => $courseId,
            'enrolledStudents' => $enrolledStudents,
            'pendingEnrollmentsList' => $pendingEnrollmentsList,
            'totalEnrolled' => $totalEnrolled,
            'pendingEnrollments' => $pendingEnrollments,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('admin/course_students', $data);
    }

    /**
     * Remove student from course (admin function)
     */
    public function removeStudentFromCourse()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $courseId = $this->request->getPost('course_id');
            $studentId = $this->request->getPost('student_id');

            // Validate inputs
            if (empty($courseId) || empty($studentId)) {
                $this->session->setFlashdata('error', 'Invalid course or student ID.');
                return redirect()->back();
            }

            // Check if enrollment exists
            $enrollmentsBuilder = $this->db->table('enrollments');
            $enrollment = $enrollmentsBuilder
                ->where('course_id', $courseId)
                ->where('user_id', $studentId)
                ->where('status', 'approved')
                ->get()
                ->getRowArray();

            if (!$enrollment) {
                $this->session->setFlashdata('error', 'Student is not enrolled in this course.');
                return redirect()->back();
            }

            // Get student and course names for notification
            $student = $this->db->table('users')->where('id', $studentId)->get()->getRowArray();
            $course = $this->db->table('courses')->where('id', $courseId)->get()->getRowArray();

            // Remove enrollment
            if ($enrollmentsBuilder->where('id', $enrollment['id'])->delete()) {
                // Send notification to student
                if ($student && $course) {
                    $adminName = $this->session->get('name');
                    $notificationMessage = "You have been removed from the course '{$course['title']}' by {$adminName}.";
                    $this->notificationModel->createNotification($studentId, $notificationMessage);
                }

                $this->session->setFlashdata('success', 'Student removed from course successfully.');
            } else {
                $this->session->setFlashdata('error', 'Failed to remove student from course.');
            }
        }

        return redirect()->back();
    }

    /**
     * Approve enrollment request (admin function)
     */
    public function approveEnrollment()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $enrollmentId = $this->request->getPost('enrollment_id');

            // Validate input
            if (empty($enrollmentId)) {
                $this->session->setFlashdata('error', 'Invalid enrollment ID.');
                return redirect()->back();
            }

            // Get enrollment details
            $enrollmentsBuilder = $this->db->table('enrollments');
            $enrollment = $enrollmentsBuilder
                ->select('enrollments.*, courses.title as course_title, users.name as student_name')
                ->join('courses', 'courses.id = enrollments.course_id', 'inner')
                ->join('users', 'users.id = enrollments.user_id', 'inner')
                ->where('enrollments.id', $enrollmentId)
                ->where('enrollments.status', 'pending')
                ->get()
                ->getRowArray();

            if (!$enrollment) {
                $this->session->setFlashdata('error', 'Enrollment request not found or already processed.');
                return redirect()->back();
            }

            // Approve enrollment
            if ($enrollmentsBuilder->where('id', $enrollmentId)->update(['status' => 'approved'])) {
                // Send notification to student
                $adminName = $this->session->get('name');
                $notificationMessage = "Your enrollment request for '{$enrollment['course_title']}' has been approved by {$adminName}.";
                $this->notificationModel->createNotification($enrollment['user_id'], $notificationMessage);

                $this->session->setFlashdata('success', "Enrollment approved for {$enrollment['student_name']}.");
            } else {
                $this->session->setFlashdata('error', 'Failed to approve enrollment.');
            }
        }

        return redirect()->back();
    }

    /**
     * Reject enrollment request (admin function)
     */
    public function rejectEnrollment()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $enrollmentId = $this->request->getPost('enrollment_id');

            // Validate input
            if (empty($enrollmentId)) {
                $this->session->setFlashdata('error', 'Invalid enrollment ID.');
                return redirect()->back();
            }

            // Get enrollment details
            $enrollmentsBuilder = $this->db->table('enrollments');
            $enrollment = $enrollmentsBuilder
                ->select('enrollments.*, courses.title as course_title, users.name as student_name')
                ->join('courses', 'courses.id = enrollments.course_id', 'inner')
                ->join('users', 'users.id = enrollments.user_id', 'inner')
                ->where('enrollments.id', $enrollmentId)
                ->where('enrollments.status', 'pending')
                ->get()
                ->getRowArray();

            if (!$enrollment) {
                $this->session->setFlashdata('error', 'Enrollment request not found or already processed.');
                return redirect()->back();
            }

            // Increment attempt count and reject enrollment
            $newAttemptCount = ($enrollment['attempt_count'] ?? 0) + 1;
            if ($enrollmentsBuilder->where('id', $enrollmentId)->update([
                'status' => 'rejected',
                'attempt_count' => $newAttemptCount
            ])) {
                // Send notification to student
                $adminName = $this->session->get('name');
                $notificationMessage = "Your enrollment request for '{$enrollment['course_title']}' has been rejected by {$adminName}.";
                $this->notificationModel->createNotification($enrollment['user_id'], $notificationMessage);

                $this->session->setFlashdata('success', "Enrollment rejected for {$enrollment['student_name']}.");
            } else {
                $this->session->setFlashdata('error', 'Failed to reject enrollment.');
            }
        }

        return redirect()->back();
    }



    /**
     * Invite student to course (admin function)
     */
    public function inviteStudentToCourse()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $courseId = $this->request->getPost('course_id');
            $studentId = $this->request->getPost('student_id');

            // Validate inputs
            if (empty($courseId) || empty($studentId)) {
                $this->session->setFlashdata('error', 'Invalid course or student ID.');
                return redirect()->back();
            }

            // Check if course exists
            $course = $this->db->table('courses')->where('id', $courseId)->get()->getRowArray();
            if (!$course) {
                $this->session->setFlashdata('error', 'Course not found.');
                return redirect()->back();
            }

            // Check if student exists and is active
            $student = $this->db->table('users')
                ->where('id', $studentId)
                ->where('role', 'student')
                ->where('deleted_at IS NULL')
                ->get()
                ->getRowArray();

            if (!$student) {
                $this->session->setFlashdata('error', 'Student not found or inactive.');
                return redirect()->back();
            }

            // Check if student is enrolled in any program
            $studentProgram = $this->studentProgramModel->getStudentProgram($studentId);
            if (!$studentProgram) {
                $this->session->setFlashdata('error', 'Cannot invite student. Student must be enrolled in an academic program first.');
                return redirect()->back();
            }

            // Check if student has active enrollment
            $activeEnrollment = $this->db->table('enrollments')
                ->where('user_id', $studentId)
                ->where('course_id', $courseId)
                ->whereIn('status', ['pending', 'approved', 'confirmed'])
                ->get()
                ->getRowArray();

            if ($activeEnrollment) {
                $statusText = ucfirst($activeEnrollment['status']);
                $this->session->setFlashdata('error', "Student is already enrolled or has a {$statusText} enrollment for this course.");
                return redirect()->back();
            }

            // Check if student has any previous enrollment (rejected/declined)
            $previousEnrollment = $this->db->table('enrollments')
                ->where('user_id', $studentId)
                ->where('course_id', $courseId)
                ->get()
                ->getRowArray();

            $enrollmentsBuilder = $this->db->table('enrollments');
            
            if ($previousEnrollment) {
                // Update existing enrollment to approved (re-invitation)
                $updateData = [
                    'status' => 'approved',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $success = $enrollmentsBuilder->where('id', $previousEnrollment['id'])->update($updateData);
            } else {
                // Create new approved enrollment (first invitation)
                $enrollmentData = [
                    'user_id' => $studentId,
                    'course_id' => $courseId,
                    'status' => 'approved',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $success = $enrollmentsBuilder->insert($enrollmentData);
            }

            if ($success) {
                // Send notification to student
                $adminName = $this->session->get('name');
                $notificationMessage = "You have been invited to enroll in '{$course['title']}' by {$adminName}. Please accept or decline the invitation.";
                $this->notificationModel->createNotification($studentId, $notificationMessage);

                $this->session->setFlashdata('success', "Invitation sent to {$student['name']} successfully!");
            } else {
                $this->session->setFlashdata('error', 'Failed to send invitation. Please try again.');
            }
        }

        return redirect()->back();
    }



    /**
     * Program Management - List all programs (admin function)
     */
    public function managePrograms()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Get all programs with course count
        $programs = $this->programModel->getProgramsWithCourseCount();

        $data = [
            'title' => 'Program Management - Admin Panel',
            'programs' => $programs,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('admin/manage_programs', $data);
    }

    /**
     * Create new program (admin function)
     */
    public function createProgram()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $programCode = strtoupper($this->request->getPost('program_code'));
            
            // Check if program code already exists
            $existingProgram = $this->programModel->where('program_code', $programCode)->first();
            if ($existingProgram) {
                $this->session->setFlashdata('error', 'Program code already exists. Please use a different code.');
                return redirect()->to(base_url('admin/programs'));
            }
            
            $programData = [
                'program_code' => $programCode,
                'program_name' => $this->request->getPost('program_name'),
                'description' => $this->request->getPost('description'),
                'duration_years' => $this->request->getPost('duration_years'),
                'total_units' => $this->request->getPost('total_units'),
                'status' => $this->request->getPost('status')
            ];

            if ($this->programModel->insert($programData)) {
                $this->session->setFlashdata('success', 'Program created successfully!');
            } else {
                $errors = $this->programModel->errors();
                $this->session->setFlashdata('error', 'Failed to create program: ' . implode(', ', $errors));
            }
        }

        return redirect()->to(base_url('admin/programs'));
    }

    /**
     * Update program (admin function)
     */
    public function updateProgram()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $programId = $this->request->getPost('program_id');
            $programCode = strtoupper($this->request->getPost('program_code'));
            
            // Check if program code already exists (excluding current program)
            $existingProgram = $this->programModel->where('program_code', $programCode)
                                                 ->where('id !=', $programId)
                                                 ->first();
            if ($existingProgram) {
                $this->session->setFlashdata('error', 'Program code already exists. Please use a different code.');
                return redirect()->to(base_url('admin/programs'));
            }
            
            $programData = [
                'program_code' => $programCode,
                'program_name' => $this->request->getPost('program_name'),
                'description' => $this->request->getPost('description'),
                'duration_years' => $this->request->getPost('duration_years'),
                'total_units' => $this->request->getPost('total_units'),
                'status' => $this->request->getPost('status')
            ];

            if ($this->programModel->update($programId, $programData)) {
                $this->session->setFlashdata('success', 'Program updated successfully!');
            } else {
                $errors = $this->programModel->errors();
                $this->session->setFlashdata('error', 'Failed to update program: ' . implode(', ', $errors));
            }
        }

        return redirect()->to(base_url('admin/programs'));
    }

    /**
     * Delete program (admin function)
     */
    public function deleteProgram()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $programId = $this->request->getPost('program_id');

            // Check if program has enrolled students
            $enrolledStudents = $this->studentProgramModel->where('program_id', $programId)
                                                          ->where('status', 'active')
                                                          ->countAllResults();

            if ($enrolledStudents > 0) {
                $this->session->setFlashdata('error', "Cannot delete program. It has {$enrolledStudents} enrolled student(s).");
                return redirect()->to(base_url('admin/programs'));
            }

            if ($this->programModel->delete($programId)) {
                $this->session->setFlashdata('success', 'Program deleted successfully!');
            } else {
                $this->session->setFlashdata('error', 'Failed to delete program.');
            }
        }

        return redirect()->to(base_url('admin/programs'));
    }

    /**
     * Manage program courses (admin function)
     */
    public function manageProgramCourses($programId)
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Get program details
        $program = $this->programModel->find($programId);
        if (!$program) {
            $this->session->setFlashdata('error', 'Program not found.');
            return redirect()->to(base_url('admin/programs'));
        }

        // Get program curriculum organized by year and semester
        $curriculum = $this->programModel->getProgramCurriculum($programId);

        // Get all available courses not in this program
        $assignedCourseIds = [];
        $programCourses = $this->programModel->getProgramCourses($programId);
        foreach ($programCourses as $course) {
            $assignedCourseIds[] = $course['course_id'];
        }

        // Get available courses grouped by title (no duplicate sections)
        $coursesBuilder = $this->db->table('courses');
        $allCourses = $coursesBuilder
            ->select('courses.*')
            ->where('courses.status', 'active');
        
        if (!empty($assignedCourseIds)) {
            $allCourses->whereNotIn('courses.id', $assignedCourseIds);
        }
        
        $allCourses = $allCourses
            ->orderBy('courses.title', 'ASC')
            ->orderBy('courses.created_at', 'ASC')
            ->get()
            ->getResultArray();

        // Group courses by title to avoid duplicate sections
        $availableCourses = [];
        $seenTitles = [];
        
        foreach ($allCourses as $course) {
            $title = $course['title'];
            if (!in_array($title, $seenTitles)) {
                $availableCourses[] = $course;
                $seenTitles[] = $title;
            }
        }

        $data = [
            'title' => 'Manage Program Courses - ' . $program['program_name'],
            'program' => $program,
            'curriculum' => $curriculum,
            'availableCourses' => $availableCourses,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('admin/program_courses', $data);
    }

    /**
     * Add course to program (admin function)
     */
    public function addCourseToProgram($programId)
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $courseId = $this->request->getPost('course_id');
            $yearLevel = $this->request->getPost('year_level');
            $semester = $this->request->getPost('semester');
            $isRequired = $this->request->getPost('is_required') ? 1 : 0;
            $prerequisiteCourseId = $this->request->getPost('prerequisite_course_id');

            // Validate inputs
            if (empty($courseId) || empty($yearLevel) || empty($semester)) {
                $this->session->setFlashdata('error', 'Please fill in all required fields.');
                return redirect()->back();
            }

            // Check if course is already in this program
            $existingAssignment = $this->db->table('program_courses')
                ->where('program_id', $programId)
                ->where('course_id', $courseId)
                ->get()
                ->getRowArray();

            if ($existingAssignment) {
                $this->session->setFlashdata('error', 'Course is already assigned to this program.');
                return redirect()->back();
            }

            // Insert course assignment
            $assignmentData = [
                'program_id' => $programId,
                'course_id' => $courseId,
                'year_level' => $yearLevel,
                'semester' => $semester,
                'is_required' => $isRequired,
                'prerequisite_course_id' => !empty($prerequisiteCourseId) ? $prerequisiteCourseId : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->db->table('program_courses')->insert($assignmentData)) {
                $this->session->setFlashdata('success', 'Course added to program successfully!');
            } else {
                $this->session->setFlashdata('error', 'Failed to add course to program.');
            }
        }

        return redirect()->back();
    }

    /**
     * Remove course from program (admin function)
     */
    public function removeCourseFromProgram()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $programCourseId = $this->request->getPost('program_course_id');

            if ($this->db->table('program_courses')->where('id', $programCourseId)->delete()) {
                $this->session->setFlashdata('success', 'Course removed from program successfully!');
            } else {
                $this->session->setFlashdata('error', 'Failed to remove course from program.');
            }
        }

        return redirect()->back();
    }

    /**
     * Debug method to check enrollments data
     */
    public function debugEnrollments()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Access denied']);
        }

        // Get all enrollments
        $enrollmentsBuilder = $this->db->table('enrollments');
        $enrollments = $enrollmentsBuilder
            ->select('enrollments.*, users.name as student_name, courses.title as course_title')
            ->join('users', 'users.id = enrollments.user_id', 'left')
            ->join('courses', 'courses.id = enrollments.course_id', 'left')
            ->get()
            ->getResultArray();

        // Get student ID 3 (Dave Villanueva) specific enrollments
        $studentEnrollments = $enrollmentsBuilder
            ->select('enrollments.*, courses.title as course_title')
            ->join('courses', 'courses.id = enrollments.course_id', 'left')
            ->where('enrollments.user_id', 3)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'all_enrollments' => $enrollments,
            'student_3_enrollments' => $studentEnrollments,
            'total_enrollments' => count($enrollments)
        ]);
    }

    /**
     * Manage student program enrollments (admin function)
     */
    public function manageStudentPrograms()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Get all students first
        $allStudents = $this->db->table('users')
            ->select('*')
            ->where('role', 'student')
            ->where('deleted_at IS NULL')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $students = [];
        $unenrolledStudents = [];

        // Process each student to get their latest program enrollment
        foreach ($allStudents as $student) {
            $latestEnrollment = $this->db->table('student_programs')
                ->select('student_programs.*, programs.program_code, programs.program_name')
                ->join('programs', 'programs.id = student_programs.program_id', 'left')
                ->where('student_programs.student_id', $student['id'])
                ->orderBy('student_programs.id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            if ($latestEnrollment) {
                // Student has program enrollment - merge data
                $students[] = array_merge($student, [
                    'program_id' => $latestEnrollment['program_id'],
                    'student_number' => $latestEnrollment['student_number'],
                    'current_year_level' => $latestEnrollment['current_year_level'],
                    'current_semester' => $latestEnrollment['current_semester'],
                    'academic_year' => $latestEnrollment['academic_year'],
                    'enrollment_status' => $latestEnrollment['status'],
                    'created_at' => $latestEnrollment['created_at'],
                    'program_code' => $latestEnrollment['program_code'],
                    'program_name' => $latestEnrollment['program_name']
                ]);
            } else {
                // Student has no program enrollment
                $unenrolledStudents[] = $student;
            }
        }

        // Get all active programs
        $programs = $this->programModel->getActivePrograms();

        $data = [
            'title' => 'Student Program Enrollments - Admin Panel',
            'students' => $students,
            'programs' => $programs,
            'unenrolledStudents' => $unenrolledStudents,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('admin/student_programs', $data);
    }

    /**
     * Enroll student in program (admin function)
     */
    public function enrollStudentInProgram()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $studentId = $this->request->getPost('student_id');
            $programId = $this->request->getPost('program_id');
            $yearLevel = $this->request->getPost('year_level');
            $semester = $this->request->getPost('semester');
            $academicYear = $this->request->getPost('academic_year');

            // Validation
            if (empty($studentId) || empty($programId) || empty($yearLevel) || empty($semester) || empty($academicYear)) {
                $this->session->setFlashdata('error', 'Please fill in all required fields.');
                return redirect()->back();
            }

            // Check if student is already enrolled in a program
            $existingEnrollment = $this->studentProgramModel
                ->where('student_id', $studentId)
                ->where('status', 'active')
                ->first();

            if ($existingEnrollment) {
                $this->session->setFlashdata('error', 'Student is already enrolled in a program.');
                return redirect()->back();
            }

            // Get program details for student number generation
            $program = $this->programModel->find($programId);
            if (!$program) {
                $this->session->setFlashdata('error', 'Invalid program selected.');
                return redirect()->back();
            }

            // Prepare enrollment data
            $enrollmentData = [
                'student_id' => $studentId,
                'program_id' => $programId,
                'enrollment_date' => date('Y-m-d'),
                'current_year_level' => $yearLevel,
                'current_semester' => $semester,
                'academic_year' => $academicYear,
                'status' => 'active'
            ];

            // Enroll student
            if ($this->studentProgramModel->enrollStudentInProgram($enrollmentData)) {
                // Get student details for notification
                $student = $this->db->table('users')->where('id', $studentId)->get()->getRowArray();
                
                // Send notification to student
                $notificationMessage = "You have been enrolled in the {$program['program_name']} ({$program['program_code']}) program. Welcome to your academic journey!";
                $this->notificationModel->createNotification($studentId, $notificationMessage);
                
                $this->session->setFlashdata('success', "Student {$student['name']} has been successfully enrolled in {$program['program_name']}!");
            } else {
                $this->session->setFlashdata('error', 'Failed to enroll student in program.');
            }
        }

        return redirect()->back();
    }

    /**
     * Update student program enrollment (admin function)
     */
    public function updateStudentProgram()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Only handle POST requests
        if ($this->request->getMethod() === 'POST') {
            $studentId = $this->request->getPost('student_id');
            $yearLevel = $this->request->getPost('year_level');
            $semester = $this->request->getPost('semester');
            $academicYear = $this->request->getPost('academic_year');
            $status = $this->request->getPost('status');

            // Get the latest student program record (same logic as in manageStudentPrograms)
            $existingProgram = $this->studentProgramModel
                ->where('student_id', $studentId)
                ->orderBy('id', 'DESC')
                ->first();
            
            if (!$existingProgram) {
                $this->session->setFlashdata('error', 'Student program enrollment not found.');
                return redirect()->back();
            }

            // Update student program
            $updateData = [
                'current_year_level' => $yearLevel,
                'current_semester' => $semester,
                'academic_year' => $academicYear,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            try {
                $result = $this->studentProgramModel->update($existingProgram['id'], $updateData);
                if ($result) {
                    $this->session->setFlashdata('success', 'Student program information updated successfully!');
                } else {
                    $this->session->setFlashdata('error', 'Failed to update student program information.');
                }
            } catch (Exception $e) {
                log_message('error', 'Error updating student program: ' . $e->getMessage());
                $this->session->setFlashdata('error', 'An error occurred while updating student program information.');
            }
        }

        return redirect()->back();
    }

    /**
     * Clean up enrollments for students not enrolled in any program (admin function)
     */
    public function cleanupUnprogrammedEnrollments()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        // Get all students who are NOT enrolled in any program
        $unprogrammedStudents = $this->db->table('users')
            ->select('users.id, users.name, users.email')
            ->join('student_programs', 'student_programs.student_id = users.id AND student_programs.status = "active"', 'left')
            ->where('users.role', 'student')
            ->where('users.deleted_at IS NULL')
            ->where('student_programs.id IS NULL')
            ->get()
            ->getResultArray();

        if (empty($unprogrammedStudents)) {
            $this->session->setFlashdata('info', 'No cleanup needed. All students with enrollments are properly enrolled in programs.');
            return redirect()->back();
        }

        $unprogrammedStudentIds = array_column($unprogrammedStudents, 'id');

        // Get enrollments for these students
        $enrollmentsToRemove = $this->db->table('enrollments')
            ->select('enrollments.*, courses.title as course_title, users.name as student_name')
            ->join('courses', 'courses.id = enrollments.course_id', 'inner')
            ->join('users', 'users.id = enrollments.user_id', 'inner')
            ->whereIn('enrollments.user_id', $unprogrammedStudentIds)
            ->get()
            ->getResultArray();

        if (empty($enrollmentsToRemove)) {
            $this->session->setFlashdata('info', 'No enrollments found for students without program enrollment.');
            return redirect()->back();
        }

        // Remove the enrollments
        $removedCount = $this->db->table('enrollments')
            ->whereIn('user_id', $unprogrammedStudentIds)
            ->delete();

        // Create notifications for affected students
        foreach ($unprogrammedStudents as $student) {
            $notificationMessage = "Your course enrollments have been removed because you are not enrolled in an academic program. Please contact the administrator to enroll in a program (BSIT, BSCS, etc.) before enrolling in courses.";
            $this->notificationModel->createNotification($student['id'], $notificationMessage);
        }

        $this->session->setFlashdata('success', "Cleanup completed! Removed {$removedCount} enrollment(s) from " . count($unprogrammedStudents) . " student(s) who are not enrolled in any program. Affected students have been notified.");
        
        return redirect()->back();
    }

    /**
     * Simple cleanup method that can be called directly via URL
     */
    public function doCleanupNow()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            return $this->response->setJSON(['error' => 'Access denied']);
        }

        // Get students without programs who have enrollments
        $unprogrammedStudents = $this->db->query("
            SELECT DISTINCT u.id, u.name, u.email 
            FROM users u 
            LEFT JOIN student_programs sp ON sp.student_id = u.id AND sp.status = 'active'
            INNER JOIN enrollments e ON e.user_id = u.id
            WHERE u.role = 'student' 
            AND u.deleted_at IS NULL 
            AND sp.id IS NULL
        ")->getResultArray();

        if (empty($unprogrammedStudents)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'No cleanup needed. All students with enrollments are properly enrolled in programs.',
                'removed' => 0
            ]);
        }

        $unprogrammedStudentIds = array_column($unprogrammedStudents, 'id');
        
        // Remove enrollments
        $placeholders = str_repeat('?,', count($unprogrammedStudentIds) - 1) . '?';
        $result = $this->db->query("DELETE FROM enrollments WHERE user_id IN ($placeholders)", $unprogrammedStudentIds);
        
        $removedCount = $this->db->affectedRows();

        return $this->response->setJSON([
            'success' => true,
            'message' => "Cleanup completed! Removed {$removedCount} enrollment(s) from " . count($unprogrammedStudents) . " student(s).",
            'removed' => $removedCount,
            'students' => $unprogrammedStudents
        ]);
    }

    /**
     * Test cleanup method with HTML output
     */
    public function testCleanup()
    {
        // Authorization check for admin role
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            return 'Access denied. Admin only.';
        }

        echo "<h2>Enrollment Cleanup Test</h2>";
        
        // Check current problematic enrollments
        $problematicEnrollments = $this->db->query("
            SELECT u.id, u.name, u.email, c.title as course_title, e.status
            FROM users u 
            LEFT JOIN student_programs sp ON sp.student_id = u.id AND sp.status = 'active'
            INNER JOIN enrollments e ON e.user_id = u.id
            INNER JOIN courses c ON c.id = e.course_id
            WHERE u.role = 'student' 
            AND u.deleted_at IS NULL 
            AND sp.id IS NULL
            ORDER BY u.name, c.title
        ")->getResultArray();

        if (empty($problematicEnrollments)) {
            echo "<p style='color: green;'>✅ No problematic enrollments found. All students with enrollments are properly enrolled in programs.</p>";
            echo "<p><a href='" . base_url('admin/dashboard') . "'>← Back to Dashboard</a></p>";
            return;
        }

        echo "<h3>Found " . count($problematicEnrollments) . " problematic enrollments:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Student</th><th>Email</th><th>Course</th><th>Status</th></tr>";
        
        foreach ($problematicEnrollments as $enrollment) {
            echo "<tr>";
            echo "<td>{$enrollment['name']}</td>";
            echo "<td>{$enrollment['email']}</td>";
            echo "<td>{$enrollment['course_title']}</td>";
            echo "<td>{$enrollment['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "<br><h3>Actions:</h3>";
        echo "<p><a href='" . base_url('admin/do-cleanup-now') . "' style='background: red; color: white; padding: 10px; text-decoration: none;' onclick='return confirm(\"Remove all these enrollments?\")'>🧹 CLEANUP NOW</a></p>";
        echo "<p><a href='" . base_url('admin/dashboard') . "'>← Back to Dashboard</a></p>";
    }
}