<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;

class Course extends BaseController
{
    protected $enrollmentModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
    }

    /**
     * Handle course enrollment via AJAX
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    public function enroll()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please log in to enroll in courses.'
            ])->setStatusCode(401);
        }

        // Check if request method is POST
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method.'
            ])->setStatusCode(405);
        }

        // Get course_id from POST request
        $course_id = $this->request->getPost('course_id');
        
        // Validate course_id
        if (empty($course_id) || !is_numeric($course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID.'
            ])->setStatusCode(400);
        }

        // Get user ID from session
        $user_id = (int) $session->get('userID');

        // Check if user is already enrolled
        if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ])->setStatusCode(400);
        }

        // Prepare enrollment data
        $enrollmentData = [
            'user_id' => $user_id,
            'course_id' => (int) $course_id,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        // Insert the new enrollment record
        $result = $this->enrollmentModel->enrollUser($enrollmentData);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in the course!',
                'enrollment_id' => $result
            ])->setStatusCode(200);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll in the course. Please try again.'
            ])->setStatusCode(500);
        }
    }
}

