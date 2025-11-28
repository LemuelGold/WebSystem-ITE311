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
          $.post("' . base_url('course/enroll') . '", {
            course_id: courseId,
            csrf_test_name: csrfToken
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
            console.error("Error:", error);
            
            // Show Bootstrap error alert
            const alertHtml = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">" +
              "<i class=\"fas fa-exclamation-circle me-2\"></i>An error occurred. Please try again." +
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

