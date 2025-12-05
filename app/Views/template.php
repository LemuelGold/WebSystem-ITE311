<?php
$userRole = strtolower(session()->get('role') ?? 'user');
$isAdmin = ($userRole === 'admin');
$isTeacher = ($userRole === 'teacher');
$isStudent = ($userRole === 'student');

// Role-specific content
if ($isAdmin) {
    $manageUsersUrl = base_url('admin/users');
    $role_content = '
      <!-- Admin Features -->
      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <div class="card shadow-sm h-100" style="border: 2px solid #000 !important;">
            <div class="card-body">
              <h5 class="fw-bold mb-3"><i class="fas fa-users-cog me-2 text-danger"></i>User Management</h5>
              <p class="text-muted mb-3">Manage all users, roles, and permissions in the system.</p>
              <a href="' . $manageUsersUrl . '" class="btn btn-outline-danger" style="border: 2px solid #000;">Manage Users</a>
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
    $enrolledCourses = $enrolledCourses ?? [];
    $availableCourses = $availableCourses ?? [];
    
    $role_content = '
      <!-- Alert Container -->
      <div id="enrollment-alert-container" class="mb-3"></div>

      <!-- Enrolled Courses Section -->
      <div class="card mb-4 shadow-sm" style="border: 2px solid #000 !important;">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="fas fa-book-open me-2 text-success"></i>My Enrolled Courses</h5>
          <div id="enrolled-courses-list">
          ';
    
    if (empty($enrolledCourses)) {
        $role_content .= '
          <p class="text-muted mb-0">You are not enrolled in any courses yet.</p>
        ';
    } else {
        $role_content .= '
          <div class="list-group">
        ';
        foreach ($enrolledCourses as $course) {
            $role_content .= '
            <div class="list-group-item d-flex justify-content-between align-items-center" style="border: 1px solid #000;">
              <div>
                <h6 class="mb-1 fw-bold">' . esc($course['title'] ?? 'Untitled Course') . '</h6>
                <p class="mb-1 text-muted small">' . esc(substr($course['description'] ?? '', 0, 100)) . (strlen($course['description'] ?? '') > 100 ? '...' : '') . '</p>
                <small class="text-muted">Instructor: ' . esc($course['instructor_name'] ?? 'N/A') . ' | Enrolled: ' . date('M d, Y', strtotime($course['enrollment_date'] ?? 'now')) . '</small>
              </div>
            </div>
            ';
        }
        $role_content .= '
          </div>
        ';
    }
    
    $role_content .= '
          </div>
        </div>
      </div>
    ';
    
    $role_content .= '
        </div>
      </div>

      <!-- Available Courses Section -->
      <div class="card mb-4 shadow-sm" style="border: 2px solid #000 !important;">
        <div class="card-body">
          <h5 class="fw-bold mb-3"><i class="fas fa-graduation-cap me-2 text-success"></i>Available Courses</h5>
          <div id="available-courses-list">
          ';
    
    if (empty($availableCourses)) {
        $role_content .= '
          <p class="text-muted mb-0">No available courses at the moment.</p>
        ';
    } else {
        $role_content .= '
          <div class="list-group">
        ';
        foreach ($availableCourses as $course) {
            $courseTitle = esc($course['title'] ?? 'Untitled Course');
            $courseDesc = esc(substr($course['description'] ?? '', 0, 100)) . (strlen($course['description'] ?? '') > 100 ? '...' : '');
            $instructorName = esc($course['instructor_name'] ?? 'N/A');
            
            $role_content .= '
            <div class="list-group-item d-flex justify-content-between align-items-center course-item" data-course-id="' . esc($course['id']) . '" style="border: 1px solid #000;">
              <div class="flex-grow-1">
                <h6 class="mb-1 fw-bold">' . $courseTitle . '</h6>
                <p class="mb-1 text-muted small">' . $courseDesc . '</p>
                <small class="text-muted">Instructor: ' . $instructorName . '</small>
              </div>
              <button class="btn btn-success btn-sm enroll-btn ms-3" data-course-id="' . esc($course['id']) . '" data-course-title="' . $courseTitle . '" data-course-desc="' . $courseDesc . '" data-instructor-name="' . $instructorName . '" style="border: 2px solid #000;">
                <i class="fas fa-plus me-1"></i>Enroll
              </button>
            </div>
            ';
        }
        $role_content .= '
          </div>
        ';
    }
    
    $role_content .= '
          </div>
        </div>
      </div>

      <script>
      $(document).ready(function() {
        // Handle enroll button clicks
        $(".enroll-btn").on("click", function(e) {
          e.preventDefault();
          
          const $btn = $(this);
          const courseId = $btn.data("course-id");
          const courseTitle = $btn.data("course-title");
          const courseDesc = $btn.data("course-desc");
          const instructorName = $btn.data("instructor-name");
          const originalHtml = $btn.html();
          
          // Disable button and show loading
          $btn.prop("disabled", true);
          $btn.html("<i class=\"fas fa-spinner fa-spin me-1\"></i>Enrolling...");
          
          // Get CSRF token
          const csrfToken = $("meta[name=\"csrf-token\"]").attr("content") || "";
          
          // Make AJAX POST request
          $.ajax({
            url: "' . site_url('course/enroll') . '",
            type: \"POST\",
            data: {
              course_id: courseId,
              csrf_test_name: csrfToken
            },
            dataType: \"json\"
          })
          .done(function(data) {
            if (data.success) {
              // Show Bootstrap success alert
              const alertHtml = "<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">" +
                "<i class=\"fas fa-check-circle me-2\"></i>" + data.message +
                "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>" +
                "</div>";
              $("#enrollment-alert-container").html(alertHtml);
              
              // Hide the enroll button
              $btn.hide();
              
              // Remove the course item from available courses
              $btn.closest(".course-item").fadeOut(300, function() {
                $(this).remove();
                
                // Check if no more available courses
                if ($(".course-item").length === 0) {
                  $("#available-courses-list").html("<p class=\"text-muted mb-0\">No available courses at the moment.</p>");
                }
              });
              
              // Add to enrolled courses list
              const enrolledList = $("#enrolled-courses-list");
              let enrolledHtml = enrolledList.html();
              
              // Remove "not enrolled" message if exists
              if (enrolledHtml.includes("not enrolled in any courses")) {
                enrolledHtml = "<div class=\"list-group\">";
              } else if (!enrolledHtml.includes("list-group")) {
                enrolledHtml = "<div class=\"list-group\">" + enrolledHtml;
              }
              
              // Get current date
              const currentDate = new Date();
              const formattedDate = currentDate.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
              
              // Add new enrolled course item
              const newCourseHtml = "<div class=\"list-group-item d-flex justify-content-between align-items-center\" style=\"border: 1px solid #000;\">" +
                "<div>" +
                "<h6 class=\"mb-1 fw-bold\">" + courseTitle + "</h6>" +
                "<p class=\"mb-1 text-muted small\">" + courseDesc + "</p>" +
                "<small class=\"text-muted\">Instructor: " + instructorName + " | Enrolled: " + formattedDate + "</small>" +
                "</div>" +
                "</div>";
              
              enrolledHtml += newCourseHtml;
              
              // Close list-group if opened
              if (enrolledHtml.includes("<div class=\"list-group\">") && !enrolledHtml.includes("</div></div>")) {
                enrolledHtml += "</div>";
              }
              
              enrolledList.html(enrolledHtml);
              
              // Auto-dismiss alert after 5 seconds
              setTimeout(function() {
                $(".alert").fadeOut(300, function() {
                  $(this).remove();
                });
              }, 5000);
              
            } else {
              // Show Bootstrap error alert
              const alertHtml = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">" +
                "<i class=\"fas fa-exclamation-circle me-2\"></i>" + data.message +
                "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>" +
                "</div>";
              $("#enrollment-alert-container").html(alertHtml);
              
              // Re-enable button
              $btn.prop("disabled", false);
              $btn.html(originalHtml);
              
              // Auto-dismiss alert after 5 seconds
              setTimeout(function() {
                $(".alert").fadeOut(300, function() {
                  $(this).remove();
                });
              }, 5000);
            }
          })
          .fail(function(xhr, status, error) {
            console.error("Error:", error, "Status:", xhr.status, "Response:", xhr.responseText);
            
            let errorMessage = "An error occurred. Please try again.";
            
            // Handle specific error cases
            if (xhr.status === 405) {
              errorMessage = "Method not allowed. Please refresh the page and try again.";
            } else if (xhr.status === 401) {
              errorMessage = "Please log in to enroll in courses.";
            } else if (xhr.status === 400) {
              try {
                const response = JSON.parse(xhr.responseText);
                errorMessage = response.message || errorMessage;
              } catch (e) {
                errorMessage = "Invalid request. Please check your input.";
              }
            } else if (xhr.status === 500) {
              errorMessage = "Server error. Please try again later.";
            }
            
            // Show Bootstrap error alert
            const alertHtml = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">" +
              "<i class=\"fas fa-exclamation-circle me-2\"></i>" + errorMessage +
              "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>" +
              "</div>";
            $("#enrollment-alert-container").html(alertHtml);
            
            // Re-enable button
            $btn.prop("disabled", false);
            $btn.html(originalHtml);
            
            // Auto-dismiss alert after 5 seconds
            setTimeout(function() {
              $(".alert").fadeOut(300, function() {
                $(this).remove();
              });
            }, 5000);
          });
        });
      });
      </script>
    ';
}

// Set title for the template
$title = 'Dashboard - LMS';

// Include the template
include(APPPATH . 'Views/template.php');
?>

You sent
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <title><?= $title ?? 'LMS Dashboard' ?></title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- jQuery (load before body content to support inline scripts) -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light text-dark">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
  <div class="container">
    <div class="d-flex align-items-center">
      <a class="navbar-brand fw-bold me-3" href="<?= base_url('/') ?>"><i class="fas fa-graduation-cap me-2"></i>LMS</a>
      <?php if (session()->get('isLoggedIn')): ?>
        <?php 
        $current_uri = uri_string();
        $is_dashboard = ($current_uri == 'dashboard');
        ?>
        <a class="nav-link<?= $is_dashboard ? ' active fw-semibold' : '' ?>" href="<?= base_url('/dashboard') ?>" style="color: inherit; text-decoration: none;">Dashboard</a>
      <?php endif; ?>
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: #000;">
      <span class="navbar-toggler-icon" style="background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%280, 0, 0, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e\");"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (session()->get('isLoggedIn')): ?>
          <li class="nav-item"><a class="nav-link text-danger" href="<?= base_url('/logout') ?>" onclick="return confirm('Are you sure you want to logout?')">Logout</a></li>
        <?php else: ?>
          <?php 
          $current_uri = uri_string();
          $is_home = ($current_uri == '' || $current_uri == 'home');
          $is_about = ($current_uri == 'about');
          $is_contact = ($current_uri == 'contact');
          $is_login = ($current_uri == 'login');
          $is_register = ($current_uri == 'register');
          ?>
          <li class="nav-item"><a class="nav-link<?= $is_home ? ' active fw-bold' : '' ?>" href="<?= base_url('/') ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link<?= $is_about ? ' active fw-bold' : '' ?>" href="<?= base_url('/about') ?>">About</a></li>
          <li class="nav-item"><a class="nav-link<?= $is_contact ? ' active fw-bold' : '' ?>" href="<?= base_url('/contact') ?>">Contact</a></li>
          <li class="nav-item"><a class="nav-link<?= $is_login ? ' active fw-bold' : '' ?>" href="<?= base_url('/login') ?>">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container py-5">

  <?php if (isset($content)): ?>
    <!-- Custom Content -->
    <?= $content ?>
  <?php else: ?>
    <!-- Default Dashboard Content -->
    <?php 
    $userRole = strtolower(session()->get('role') ?? 'user');
    $isAdmin = ($userRole === 'admin');
    $isTeacher = ($userRole === 'teacher');
    $isStudent = ($userRole === 'student');
    
    // Set role-specific colors and icons
    if ($isAdmin) {
        $roleColor = 'danger';
        $roleIcon = 'user-shield';
        $welcomeText = 'Manage the entire learning management system';
    } elseif ($isTeacher) {
        $roleColor = 'primary';
        $roleIcon = 'chalkboard-teacher';
        $welcomeText = 'Manage your teaching activities and students';
    } else {
        $roleColor = 'success';
        $roleIcon = 'graduation-cap';
        $welcomeText = 'Manage your learning activities efficiently';
    }
    ?>
    
    <!-- Welcome Card -->
   <div class="card mb-4 shadow-sm"style="border: 2px solid #000 !important;">
      <div class="card-body text-center py-4">
        <h2 class="fw-bold mb-3">
          <i class="fas fa-<?= $roleIcon ?> me-2 text-<?= $roleColor ?>"></i>
          Welcome, <?= session()->get('name') ?? 'User' ?>!
        </h2>
        <p class="text-muted mb-0">
          <?= $welcomeText ?>
        </p>
        <p class="text-muted small mt-2">
          <span class="badge bg-<?= $roleColor ?>">
            <?= ucfirst(session()->get('role') ?? 'User') ?>
          </span>
        </p>
      </div>
    </div>

    <!-- User Info -->
    <div class="card mb-4 shadow-sm">
      <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="fas fa-user-circle me-2"></i>Account Information</h5>
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between bg-transparent border-bottom">
            <span class="fw-semibold text-dark">Full Name</span>
            <span class="text-dark"><?= session()->get('name') ?? 'Guest' ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between bg-transparent border-bottom">
            <span class="fw-semibold text-dark">Email</span>
            <span class="text-dark"><?= session()->get('email') ?? 'N/A' ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between bg-transparent">
            <span class="fw-semibold text-dark">Role</span>
            <span class="badge bg-<?= $roleColor ?>">
              <?= ucfirst(session()->get('role') ?? 'User') ?>
            </span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Role-specific Features -->
    <?php if (isset($role_content)): ?>
      <?= $role_content ?>
    <?php endif; ?>
  <?php endif; ?>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php
include(APPPATH . 'Views/template.php');