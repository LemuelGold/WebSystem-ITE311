<?php

namespace App\Controllers;

use App\Models\MaterialModel;

/**
 * Materials Controller - Handles file upload, download, and deletion for course materials
 */
class Materials extends BaseController
{
    protected $session;
    protected $db;
    protected $materialModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->materialModel = new MaterialModel();
    }

    /**
     * Display upload form and handle file upload
     */
    public function upload($course_id)
    {
        // Authorization check - only admin and teachers can upload
        if (!$this->isLoggedIn() || !in_array($this->session->get('role'), ['admin', 'teacher'])) {
            $this->session->setFlashdata('error', 'Access denied. Only admins and teachers can upload materials.');
            return redirect()->to(base_url('login'));
        }

        // If teacher, verify they own this course
        if ($this->session->get('role') === 'teacher') {
            $course = $this->db->table('courses')
                              ->where('id', $course_id)
                              ->where('instructor_id', $this->session->get('userID'))
                              ->get()
                              ->getRowArray();
            
            if (!$course) {
                $this->session->setFlashdata('error', 'Access denied. You can only upload materials to your own courses.');
                return redirect()->to(base_url('teacher/courses'));
            }
        }

        // Get course info
        $course = $this->db->table('courses')->where('id', $course_id)->get()->getRowArray();
        
        if (!$course) {
            $this->session->setFlashdata('error', 'Course not found.');
            return redirect()->back();
        }

        // Handle POST request (file upload)
        if ($this->request->getMethod() === 'POST') {
            return $this->handleUpload($course_id);
        }

        // Get existing materials
        $materials = $this->materialModel->getMaterialsByCourse($course_id);

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
                'rules' => 'uploaded[material_file]|max_size[material_file,10240]|ext_in[material_file,pdf,doc,docx,ppt,pptx,txt,zip,rar]',
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
                // Prepare data for database
                $materialData = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => 'uploads/materials/' . $newName
                ];

                // Save to database
                if ($this->materialModel->insertMaterial($materialData)) {
                    $this->session->setFlashdata('success', 'Material uploaded successfully!');
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

        return redirect()->to(base_url("admin/course/{$course_id}/upload"));
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

        return redirect()->to(base_url("admin/course/{$material['course_id']}/upload"));
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
                                   ->where('student_id', $this->session->get('userID'))
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
     * Check if user is logged in
     */
    private function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') === true;
    }
}
