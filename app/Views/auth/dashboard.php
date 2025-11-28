<?php
$userRole = strtolower(session()->get('role') ?? 'user');
$isAdmin = ($userRole === 'admin');
$isTeacher = ($userRole === 'teacher');
$isStudent = ($userRole === 'student');

// Role-specific content
if ($isAdmin) {
    $role_content = '
      <!-- Admin Features -->
      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <div class="card shadow-sm h-100" style="border: 2px solid #000 !important;">
            <div class="card-body">
              <h5 class="fw-bold mb-3"><i class="fas fa-users-cog me-2 text-danger"></i>User Management</h5>
              <p class="text-muted mb-3">Manage all users, roles, and permissions in the system.</p>
              <a href="#" class="btn btn-outline-danger" style="border: 2px solid #000;">Manage Users</a>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card shadow-sm h-100" style="border: 2px solid #000 !important;">
            <div class="card-body">
              <h5 class="fw-bold mb-3"><i class="fas fa-book me-2 text-danger"></i>Course Management</h5>
              <p class="text-muted mb-3">Create, edit, and manage all courses in the system.</p>
              <a href="#" class="btn btn-outline-danger" style="border: 2px solid #000;">Manage Courses</a>
            </div>
          </div>
        </div>
      </div>
    ';
} elseif ($isTeacher) {
    $role_content = '
      <!-- Teacher Features -->
      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <div class="card shadow-sm h-100" style="border: 2px solid #000 !important;">
            <div class="card-body">
              <h5 class="fw-bold mb-3"><i class="fas fa-chalkboard-teacher me-2 text-primary"></i>My Courses</h5>
              <p class="text-muted mb-3">View and manage the courses you are teaching.</p>
              <a href="#" class="btn btn-outline-primary" style="border: 2px solid #000;">View Courses</a>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="card shadow-sm h-100" style="border: 2px solid #000 !important;">
            <div class="card-body">
              <h5 class="fw-bold mb-3"><i class="fas fa-user-graduate me-2 text-primary"></i>Students</h5>
              <p class="text-muted mb-3">View and manage your students and their progress.</p>
              <a href="#" class="btn btn-outline-primary" style="border: 2px solid #000;">View Students</a>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          
          </div>
        </div>
      </div>
    ';
} else {
    // Student features
    $role_content = '
      <!-- Student Features -->
      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <div class="card shadow-sm h-100" style="border: 2px solid #000 !important;">
            <div class="card-body">
              <h5 class="fw-bold mb-3"><i class="fas fa-book-open me-2 text-success"></i>My Courses</h5>
              <p class="text-muted mb-3">Access all your enrolled courses and course materials.</p>
              <a href="#" class="btn btn-outline-success" style="border: 2px solid #000;">View Courses</a>
            </div>
          </div>
        </div>
        
        
       
    ';
}

// Set title for the template
$title = 'Dashboard - LMS';

// Include the template
include(APPPATH . 'Views/template.php');
?>

