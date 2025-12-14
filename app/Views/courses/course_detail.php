<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Course Details - LMS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: white;
            min-height: 100vh;
        }
        
        .navbar {
            background-color: white !important;
            box-shadow: none;
            padding: 0.5rem 0;
            border-bottom: 2px solid #ddd;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: #333 !important;
            font-size: 1.5rem;
        }
        
        .navbar-brand::before {
            content: "🎓";
            margin-right: 8px;
        }
        
        .navbar-nav .nav-link {
            color: #666 !important;
            font-weight: 500;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #333 !important;
        }
        
        .course-header {
            background: white;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 1.2rem;
            margin-bottom: 1.2rem;
        }
        
        .course-title {
            color: #333;
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 0.8rem;
        }
        
        .course-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            margin-bottom: 0.5rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            color: #666;
            font-size: 0.9rem;
        }
        
        .meta-item i {
            margin-right: 0.5rem;
            color: #999;
        }
        
        .course-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .course-content {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.2rem;
        }
        
        .course-description {
            color: #333;
            line-height: 1.5;
            font-size: 0.95rem;
        }
        
        .enrollment-section {
            background: white;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 1.2rem;
            text-align: center;
        }
        
        .enrollment-status {
            margin-bottom: 0.8rem;
        }
        
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .status-enrolled {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-not-enrolled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .btn-enroll {
            background-color: #198754;
            border-color: #198754;
            color: white;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .btn-enroll:hover {
            background-color: #157347;
            border-color: #146c43;
            color: white;
        }
        
        .back-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php 
                $session = \Config\Services::session();
                $isLoggedIn = $session->get('isLoggedIn') === true;
                $userRole = $session->get('role');
                ?>
                
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <?php if ($isLoggedIn): ?>
                            <?php 
                            $dashboardUrl = base_url('dashboard');
                            switch($userRole) {
                                case 'admin':
                                    $dashboardUrl = base_url('admin/dashboard');
                                    break;
                                case 'teacher':
                                    $dashboardUrl = base_url('teacher/dashboard');
                                    break;
                                case 'student':
                                    $dashboardUrl = base_url('student/dashboard');
                                    break;
                            }
                            ?>
                            <a class="nav-link" href="<?= $dashboardUrl ?>">Dashboard</a>
                        <?php else: ?>
                            <a class="nav-link" href="<?= base_url() ?>">Home</a>
                        <?php endif; ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('courses') ?>">Courses</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('logout') ?>">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('login') ?>">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Back Link -->
        <div class="mb-2">
            <a href="<?= base_url('courses') ?>" class="back-link">
                <i class="bi bi-arrow-left"></i> Back to Courses
            </a>
        </div>

        <!-- Course Header -->
        <div class="course-header">
            <h1 class="course-title"><?= esc($course['title']) ?></h1>
            
            <div class="course-meta">
                <?php if (!empty($course['instructor_name'])): ?>
                    <div class="meta-item">
                        <i class="bi bi-person"></i>
                        <span>Instructor: <?= esc($course['instructor_name']) ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($course['section'])): ?>
                    <div class="meta-item">
                        <i class="bi bi-bookmark"></i>
                        <span>Section: <?= esc($course['section']) ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($course['schedule_time'])): ?>
                    <div class="meta-item">
                        <i class="bi bi-clock"></i>
                        <span>Schedule: <?= esc($course['schedule_time']) ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($course['room'])): ?>
                    <div class="meta-item">
                        <i class="bi bi-geo-alt"></i>
                        <span>Room: <?= esc($course['room']) ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($course['units'])): ?>
                    <div class="meta-item">
                        <i class="bi bi-award"></i>
                        <span>Units: <?= esc($course['units']) ?> Unit<?= $course['units'] > 1 ? 's' : '' ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($course['term'])): ?>
                    <div class="meta-item">
                        <i class="bi bi-calendar-range"></i>
                        <span>Term: <?= esc($course['term']) ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($course['academic_year'])): ?>
                    <div class="meta-item">
                        <i class="bi bi-calendar"></i>
                        <span>Academic Year: <?= esc($course['academic_year']) ?> - <?= esc($course['academic_year'] + 1) ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($course['semester'])): ?>
                    <div class="meta-item">
                        <i class="bi bi-calendar-event"></i>
                        <span>Semester: <?= esc($course['semester']) ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="meta-item">
                    <i class="bi bi-hash"></i>
                    <span>Course ID: <?= esc($course['course_code'] ?? $course['id']) ?></span>
                </div>
                
                <div class="meta-item">
                    <span class="course-status <?= ($course['status'] ?? 'active') === 'active' ? 'status-active' : 'status-inactive' ?>">
                        <?= ucfirst($course['status'] ?? 'Active') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Course Description -->
        <div class="course-content">
            <h5 class="mb-2">Course Description</h5>
            <div class="course-description">
                <?= nl2br(esc($course['description'] ?? 'No description available.')) ?>
            </div>
        </div>

        <!-- Enrollment Section -->
        <?php if ($isLoggedIn && $user['role'] === 'student'): ?>
            <div class="enrollment-section">
                <h5 class="mb-2">Enrollment Status</h5>
                
                <div class="enrollment-status">
                    <?php if ($enrollmentStatus === 'approved'): ?>
                        <span class="status-badge status-enrolled">
                            <i class="bi bi-check-circle"></i> Enrolled
                        </span>
                        <p class="mt-2 text-muted">You are enrolled in this course.</p>
                    <?php elseif ($enrollmentStatus === 'pending'): ?>
                        <span class="status-badge status-pending">
                            <i class="bi bi-clock"></i> Pending Approval
                        </span>
                        <p class="mt-2 text-muted">Your enrollment request is pending teacher approval.</p>
                        <div class="mt-3">
                            <form method="POST" action="<?= base_url('course/cancel-enrollment') ?>" style="display: inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to cancel your enrollment request?')">
                                    <i class="bi bi-x-circle"></i> Cancel Enrollment Request
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <span class="status-badge status-not-enrolled">
                            <i class="bi bi-x-circle"></i> Not Enrolled
                        </span>
                        
                        <?php if (($course['status'] ?? 'active') === 'active'): ?>
                            <?php if (!empty($course['instructor_id']) && $course['instructor_id'] != null): ?>
                                <?php if (isset($attemptInfo) && $attemptInfo['hasExceededAttempts']): ?>
                                    <p class="mt-2 text-danger">
                                        <i class="bi bi-exclamation-triangle"></i> 
                                        You have reached the maximum number of enrollment attempts (3) for this course.
                                    </p>
                                    <p class="mt-1 text-muted small">
                                        Total attempts made: <?= $attemptInfo['totalAttempts'] ?>/3
                                    </p>
                                <?php else: ?>
                                    <div class="mt-3">
                                        <form method="POST" action="<?= base_url('course/enroll') ?>" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                            <button type="submit" class="btn btn-enroll" onclick="return confirm('Are you sure you want to enroll in this course?')">
                                                <i class="bi bi-plus-circle"></i> Enroll in Course
                                            </button>
                                        </form>
                                    </div>
                                    <?php if (isset($attemptInfo) && $attemptInfo['totalAttempts'] > 0): ?>
                                        <p class="mt-2 text-muted small">
                                            <i class="bi bi-info-circle"></i> 
                                            Attempts remaining: <?= $attemptInfo['remainingAttempts'] ?>/3
                                        </p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="mt-2 text-muted">This course does not have an instructor assigned yet. Enrollment is not available.</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="mt-2 text-muted">This course is not available for enrollment.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif (!$isLoggedIn): ?>
            <div class="enrollment-section">
                <h5 class="mb-2">Enrollment</h5>
                <p class="text-muted mb-2">Please login to enroll in this course.</p>
                <a href="<?= base_url('login') ?>" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Login to Enroll
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if ($isLoggedIn && $user['role'] === 'student'): ?>
    <script>
        function enrollInCourse(courseId) {
            console.log('Enrolling in course:', courseId);
            
            if (confirm('Are you sure you want to enroll in this course?')) {
                // Create form data
                const formData = new FormData();
                formData.append('course_id', courseId);
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                console.log('Sending enrollment request to:', '<?= base_url('course/enroll') ?>');

                // Send enrollment request
                fetch('<?= base_url('course/enroll') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        alert(data.message);
                        location.reload(); // Refresh to show updated status
                    } else {
                        alert('Enrollment failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again. Check console for details.');
                });
            }
        }

        // Add event listener to button
        document.addEventListener('DOMContentLoaded', function() {
            const enrollBtn = document.getElementById('enrollBtn');
            if (enrollBtn) {
                enrollBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const courseId = <?= $course['id'] ?>;
                    enrollInCourse(courseId);
                });
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>