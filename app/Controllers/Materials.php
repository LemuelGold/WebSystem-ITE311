<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\MaterialModel;
use App\Models\NotificationModel;

/**
 * Materials Controller - Handles file upload, download, and deletion for course materials
 */
class Materials extends BaseController
{
    protected $session;
    protected $db;
    protected $courseModel;
    protected $materialModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->courseModel = new CourseModel();
        $this->materialModel = new MaterialModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Display upload form and handle file upload
     */
    public function upload($course_id)
    {
        // Clear any old flash messages first
        $this->session->markAsFlashdata([]);
        
        // Authorization check - only admin and teachers can upload
        if (!$this->isLoggedIn() || !in_array($this->session->get('role'), ['admin', 'teacher'])) {
            $this->session->setFlashdata('error', 'Access denied. Only admins and teachers can upload materials.');
            return redirect()->to(base_url('login'));
        }

        // If teacher, verify they own this course
        if ($this->session->get('role') === 'teacher') {
            $course = $this->courseModel
                          ->where('id', $course_id)
                          ->where('instructor_id', $this->session->get('userID'))
                          ->first();
            
            if (!$course) {
                $this->session->setFlashdata('error', 'Access denied. You can only upload materials to your own courses.');
                return redirect()->to(base_url('teacher/courses'));
            }
        }

        // Get course info
        $course = $this->courseModel->find($course_id);
        
        if (!$course) {
            $this->session->setFlashdata('error', 'Course not found.');
            return redirect()->back();
        }

        // Handle POST request (file upload)
        if ($this->request->getMethod() === 'POST') {
            return $this->handleUpload($course_id);
        }

        // Get existing materials - admin sees all, teachers see their own
        if ($this->session->get('role') === 'admin') {
            $materials = $this->materialModel->getMaterialsByCourse($course_id);
        } else {
            // Teachers only see approved and their own pending materials
            $materials = $this->materialModel->getMaterialsByCourse($course_id);
        }

        // Display upload form
        $data = [
            'title' => 'Upload Materials - ' . $course['title'],
            'course' => $course,
            'materials' => $materials,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('materials/upload', $data);
    }

    /**
     * Handle file upload process
     */
    private function handleUpload($course_id)
    {
        // Validation rules
        $validationRules = [
            'material_file' => [
                'label' => 'File',
                'rules' => 'uploaded[material_file]|max_size[material_file,10240]|ext_in[material_file,pdf,doc,docx]',
            ],
            'period' => [
                'label' => 'Academic Period',
                'rules' => 'required|in_list[Prelim,Midterm,Final]',
            ],
            'material_title' => [
                'label' => 'Material Title',
                'rules' => 'permit_empty|max_length[100]',
            ],
        ];

        if (!$this->validate($validationRules)) {
            $this->session->setFlashdata('error', $this->validator->listErrors());
            return redirect()->back()->withInput();
        }

        $file = $this->request->getFile('material_file');

        if ($file->isValid() && !$file->hasMoved()) {
            // Create upload directory if it doesn't exist
            $uploadPath = FCPATH . 'uploads/materials/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Generate unique filename
            $newName = $file->getRandomName();
            
            // Move file to upload directory
            if ($file->move($uploadPath, $newName)) {
                // Both admin and teachers can upload with auto-approval
                // Only other roles would need approval (if added in future)
                $status = 'approved';
                
                // Get form data
                $period = $this->request->getPost('period');
                $materialTitle = $this->request->getPost('material_title');
                
                // Use material title if provided, otherwise use original filename
                $displayTitle = !empty($materialTitle) ? $materialTitle : $file->getClientName();
                
                // Set default period if not provided
                if (empty($period)) {
                    $period = null;
                }
                
                // Prepare data for database
                $materialData = [
                    'course_id' => $course_id,
                    'uploaded_by' => $this->session->get('userID'),
                    'file_name' => $file->getClientName(),
                    'file_path' => 'uploads/materials/' . $newName,
                    'period' => $period,
                    'material_title' => $displayTitle,
                    'status' => $status,
                    'approved_by' => $this->session->get('userID'),
                    'approved_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                // Save to database
                if ($this->materialModel->insertMaterial($materialData)) {
                    // Send notifications to all enrolled students
                    $this->sendMaterialNotifications($course_id, $displayTitle, $period);
                    
                    if (!empty($period)) {
                        $periodIcon = '';
                        switch($period) {
                            case 'Prelim': $periodIcon = '📖'; break;
                            case 'Midterm': $periodIcon = '📚'; break;
                            case 'Final': $periodIcon = '🎓'; break;
                        }
                        $this->session->setFlashdata('success', "Material uploaded successfully for {$periodIcon} {$period} period! Students have been notified.");
                    } else {
                        $this->session->setFlashdata('success', "Material uploaded successfully! Students have been notified.");
                    }
                } else {
                    // Delete uploaded file if database insert fails
                    unlink($uploadPath . $newName);
                    $this->session->setFlashdata('error', 'Failed to save material information.');
                }
            } else {
                $this->session->setFlashdata('error', 'Failed to upload file. Please try again.');
            }
        } else {
            $this->session->setFlashdata('error', 'Invalid file or file already moved.');
        }

        // Redirect based on user role
        $role = $this->session->get('role');
        return redirect()->to(base_url("{$role}/course/{$course_id}/upload"));
    }

    /**
     * Delete a material
     */
    public function delete($material_id)
    {
        // Authorization check
        if (!$this->isLoggedIn() || !in_array($this->session->get('role'), ['admin', 'teacher'])) {
            $this->session->setFlashdata('error', 'Access denied.');
            return redirect()->to(base_url('login'));
        }

        // Get material info
        $material = $this->materialModel->getMaterialWithCourse($material_id);

        if (!$material) {
            $this->session->setFlashdata('error', 'Material not found.');
            return redirect()->back();
        }

        // If teacher, verify they own the course
        if ($this->session->get('role') === 'teacher') {
            if ($material['instructor_id'] != $this->session->get('userID')) {
                $this->session->setFlashdata('error', 'Access denied. You can only delete materials from your own courses.');
                return redirect()->back();
            }
        }

        // Delete file from server
        $filePath = FCPATH . $material['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        if ($this->materialModel->deleteMaterial($material_id)) {
            $this->session->setFlashdata('success', 'Material deleted successfully!');
        } else {
            $this->session->setFlashdata('error', 'Failed to delete material.');
        }

        // Redirect based on user role
        $role = $this->session->get('role');
        return redirect()->to(base_url("{$role}/course/{$material['course_id']}/upload"));
    }

    /**
     * Download a material file
     */
    public function download($material_id)
    {
        // Authorization check - must be logged in
        if (!$this->isLoggedIn()) {
            $this->session->setFlashdata('error', 'Please login to download materials.');
            return redirect()->to(base_url('login'));
        }

        // Get material with course info
        $material = $this->materialModel->getMaterialWithCourse($material_id);

        if (!$material) {
            $this->session->setFlashdata('error', 'Material not found.');
            return redirect()->back();
        }

        // Check material status - only students need approved materials
        if ($this->session->get('role') === 'student' && $material['status'] !== 'approved') {
            $this->session->setFlashdata('error', 'This material is pending admin approval and is not yet available for download.');
            return redirect()->back();
        }

        // Check if user has access
        $hasAccess = false;

        // Admin has access to all
        if ($this->session->get('role') === 'admin') {
            $hasAccess = true;
        }
        // Teacher has access to their own course materials
        elseif ($this->session->get('role') === 'teacher') {
            if ($material['instructor_id'] == $this->session->get('userID')) {
                $hasAccess = true;
            }
        }
        // Student must be enrolled in the course
        elseif ($this->session->get('role') === 'student') {
            $enrollment = $this->db->table('enrollments')
                                   ->where('user_id', $this->session->get('userID'))
                                   ->where('course_id', $material['course_id'])
                                   ->get()
                                   ->getRowArray();
            
            if ($enrollment) {
                $hasAccess = true;
            }
        }

        if (!$hasAccess) {
            $this->session->setFlashdata('error', 'Access denied. You must be enrolled in this course to download materials.');
            return redirect()->back();
        }

        // Download file
        $filePath = FCPATH . $material['file_path'];

        if (!file_exists($filePath)) {
            $this->session->setFlashdata('error', 'File not found on server.');
            return redirect()->back();
        }

        return $this->response->download($filePath, null)->setFileName($material['file_name']);
    }

    /**
     * Approve a pending material (Admin only)
     */
    public function approve($material_id)
    {
        // Authorization check - only admin
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Only admins can approve materials.');
            return redirect()->to(base_url('login'));
        }

        if ($this->materialModel->approveMaterial($material_id, $this->session->get('userID'))) {
            $this->session->setFlashdata('success', 'Material approved successfully!');
        } else {
            $this->session->setFlashdata('error', 'Failed to approve material.');
        }

        return redirect()->back();
    }

    /**
     * Reject a pending material (Admin only)
     */
    public function reject($material_id)
    {
        // Authorization check - only admin
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Only admins can reject materials.');
            return redirect()->to(base_url('login'));
        }

        // Get material info before rejecting
        $material = $this->materialModel->find($material_id);
        
        if ($material) {
            // Reject in database
            if ($this->materialModel->rejectMaterial($material_id, $this->session->get('userID'))) {
                // Optionally delete the file
                $filePath = FCPATH . $material['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                $this->session->setFlashdata('success', 'Material rejected and removed.');
            } else {
                $this->session->setFlashdata('error', 'Failed to reject material.');
            }
        } else {
            $this->session->setFlashdata('error', 'Material not found.');
        }

        return redirect()->back();
    }

    /**
     * View pending materials for approval (Admin only)
     */
    public function pending()
    {
        // Authorization check - only admin
        if (!$this->isLoggedIn() || $this->session->get('role') !== 'admin') {
            $this->session->setFlashdata('error', 'Access denied. Only admins can view pending materials.');
            return redirect()->to(base_url('login'));
        }

        $pendingMaterials = $this->materialModel->getPendingMaterials();

        $data = [
            'title' => 'Pending Materials - Admin Approval',
            'materials' => $pendingMaterials,
            'user' => [
                'userID' => $this->session->get('userID'),
                'name'   => $this->session->get('name'),
                'role'   => $this->session->get('role')
            ]
        ];

        return view('materials/pending', $data);
    }

    /**
     * Send notifications to enrolled students when material is uploaded
     */
    private function sendMaterialNotifications($course_id, $materialTitle, $period = null)
    {
        // Get course information
        $course = $this->courseModel->find($course_id);
        if (!$course) return;
        
        // Get all confirmed enrolled students for this course
        $enrolledStudents = $this->db->table('enrollments')
            ->select('enrollments.user_id, users.name as student_name')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.course_id', $course_id)
            ->where('enrollments.status', 'confirmed')
            ->where('users.role', 'student')
            ->where('users.deleted_at IS NULL')
            ->get()
            ->getResultArray();
        
        if (empty($enrolledStudents)) return;
        
        // Prepare notification message
        $periodText = '';
        if (!empty($period)) {
            $periodIcon = '';
            switch($period) {
                case 'Prelim': $periodIcon = '📖'; break;
                case 'Midterm': $periodIcon = '📚'; break;
                case 'Final': $periodIcon = '🎓'; break;
            }
            $periodText = " for {$periodIcon} {$period} period";
        }
        
        $uploaderName = $this->session->get('name');
        $uploaderRole = ucfirst($this->session->get('role'));
        
        $message = "📄 New material uploaded: \"{$materialTitle}\"{$periodText} in {$course['title']} by {$uploaderRole} {$uploaderName}.";
        
        // Send notification to each enrolled student
        foreach ($enrolledStudents as $student) {
            $this->notificationModel->createNotification($student['user_id'], $message);
        }
        
        log_message('info', "Material notifications sent to " . count($enrolledStudents) . " students for course {$course_id}");
    }

    /**
     * Check if user is logged in
     */
    private function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') === true;
    }
}
