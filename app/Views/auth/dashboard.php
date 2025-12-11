<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard - LMS' ?></title>
    <!-- Just using Bootstrap for styling - much easier than writing custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* This changes navbar color based on user role - pretty cool trick I learned */
        .role-<?= $user['role'] ?> .navbar { 
            background-color: <?= $user['role'] === 'admin' ? '#dc3545' : ($user['role'] === 'teacher' ? '#ffc107' : '#0d6efd') ?> !important; 
        }
        /* Welcome card also changes color - red for admin, yellow for teacher, blue for student */
        .role-<?= $user['role'] ?> .welcome-card { 
            background-color: <?= $user['role'] === 'admin' ? '#dc3545' : ($user['role'] === 'teacher' ? '#ffc107' : '#0d6efd') ?> !important; 
            color: <?= $user['role'] === 'teacher' ? '#000' : '#fff' ?> !important;
        }
        /* Force navbar text to be dark for teacher and student */
        .role-teacher .navbar .nav-link,
        .role-teacher .navbar .navbar-brand,
        .role-teacher .navbar .navbar-text,
        .role-student .navbar .nav-link,
        .role-student .navbar .navbar-brand,
        .role-student .navbar .navbar-text {
            color: #000 !important;
        }
        /* Simple hover effect for the stat cards - makes it feel more interactive */
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        /* Notification styles */
        .notification-dropdown {
            padding: 0;
        }
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s;
            cursor: pointer;
        }
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        .notification-item.unread {
            background-color: #e7f3ff;
        }
        .notification-item.unread:hover {
            background-color: #d1e8ff;
        }
        .notification-message {
            font-size: 0.9rem;
            margin-bottom: 4px;
            color: #333;
        }
        .notification-time {
            font-size: 0.75rem;
            color: #6c757d;
        }
        .notification-actions {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }
        .notification-actions .btn {
            font-size: 0.75rem;
            padding: 4px 12px;
        }
    </style>
</head>
<body class="bg-light role-<?= $user['role'] ?>">
    <!-- I made this navbar dynamic - it shows different links depending on who's logged in -->
    <nav class="navbar navbar-expand-lg <?= $user['role'] === 'admin' ? 'navbar-dark' : 'navbar-light' ?>">
        <div class="container">
            <!-- Simple text branding without fancy icons -->
            <a class="navbar-brand fw-bold" href="<?= base_url('dashboard') ?>">
                ITE311 FUNDAR LMS
            </a>
            
            <!-- Standard bootstrap hamburger menu for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <?php 
                        // Make dashboard link role-specific to avoid unnecessary redirects through filter
                        $dashboardUrl = base_url('dashboard');
                        if (isset($user['role'])) {
                            switch($user['role']) {
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
                        }
                        ?>
                        <a class="nav-link active" href="<?= $dashboardUrl ?>">
                            Dashboard
                        </a>
                    </li>
                    <?php if ($user['role'] === 'admin'): ?>
                        <!-- Admin navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/users') ?>">
                                Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/courses') ?>">
                                Courses
                            </a>
                        </li>
                    <?php elseif ($user['role'] === 'teacher'): ?>
                        <!-- Teacher navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('teacher/courses') ?>">
                                My Courses
                            </a>
                        </li>
                    <?php elseif ($user['role'] === 'student'): ?>
                        <!-- Student navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('student/courses') ?>">
                                My Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('student/materials') ?>">
                                Materials
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Notification Bell Icon -->
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                            </svg>
                            <span id="notificationBadge" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill" style="display: none; font-size: 0.65rem;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 500px; overflow-y: auto;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fa; padding: 12px 16px;">
                                <span class="fw-bold">Notifications</span>
                                <button class="btn btn-sm btn-link text-decoration-none p-0" id="markAllRead" style="display: none; font-size: 0.85rem;">Mark all as read</button>
                            </div>
                            <div class="dropdown-divider m-0"></div>
                            <div id="notificationList" class="notification-list">
                                <div class="text-center py-4 text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-bell-slash mb-2 opacity-25" viewBox="0 0 16 16">
                                        <path d="M5.164 14H15c-.299-.199-.557-.553-.78-1-.9-1.8-1.22-5.12-1.22-6 0-.264-.02-.523-.06-.776l-.938.938c.02.708.157 2.154.457 3.58.161.767.377 1.566.663 2.258H6.164l-1 1zm5.581-9.91a3.986 3.986 0 0 0-1.948-1.01L8 2.917l-.797.161A4.002 4.002 0 0 0 4 7c0 .628-.134 2.197-.459 3.742-.05.238-.105.479-.166.718l-1.653 1.653c.02-.037.04-.074.059-.113C2.679 11.2 3 7.88 3 7c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0c.942.19 1.788.645 2.457 1.284l-.707.707zM10 15a2 2 0 1 1-4 0h4zm-9.375.625a.53.53 0 0 0 .75.75l14.75-14.75a.53.53 0 0 0-.75-.75L.625 15.625z"/>
                                    </svg>
                                    <p class="mb-0 small">No notifications</p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                
                <!-- User menu on the right side - shows name and role badge -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= esc($user['name']) ?>
                            <!-- This badge shows the user's role - pretty neat visual cue -->
                            <span class="badge <?= $user['role'] === 'admin' ? 'bg-light text-dark' : 'bg-dark' ?> ms-2"><?= strtoupper($user['role']) ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Flash messages - these show up after login/logout or errors -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php 
        // Smart error filtering - only show errors relevant to the current user's role
        $errorMsg = session()->getFlashdata('error');
        $showError = false;
        
        if ($errorMsg) {
            // Don't show admin privilege errors to non-admin users
            if (strpos($errorMsg, 'Administrator privileges') !== false && $user['role'] !== 'admin') {
                // This is an admin error but user is not admin - don't show it
                $showError = false;
            }
            // Don't show teacher privilege errors to non-teacher users
            elseif (strpos($errorMsg, 'Teacher privileges') !== false && $user['role'] !== 'teacher') {
                $showError = false;
            }
            // Don't show student privilege errors to non-student users
            elseif (strpos($errorMsg, 'Student privileges') !== false && $user['role'] !== 'student') {
                $showError = false;
            }
            // Show all other errors
            else {
                $showError = true;
            }
        }
        ?>
        
        <?php if ($showError): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $errorMsg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Big welcome message - changes icon and text based on role -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card welcome-card text-white">
                    <div class="card-body">
                        <h1 class="card-title mb-0">
                            Welcome back, <?= esc($user['name']) ?>!
                        </h1>
                        <p class="card-text mb-0">
                            <?php 
                                // Different welcome message for each role - makes it feel personalized
                                switch($user['role']) {
                                    case 'admin': echo 'Manage users, courses, and system settings'; break;
                                    case 'teacher': echo 'Manage your courses and track student progress'; break;
                                    case 'student': echo 'Continue your learning journey and track your academic progress'; break;
                                }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- These stat cards show different info depending on who's logged in -->
        <?php if (isset($stats)): ?>
        <div class="row mb-4">
            <?php if ($user['role'] === 'admin'): ?>
                <!-- Admin sees total counts of everything - users, students, teachers, courses -->
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-primary"><?= $stats['totalUsers'] ?></h3>
                            <p class="mb-0">Total Users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-success"><?= $stats['studentCount'] ?></h3>
                            <p class="mb-0">Students</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-warning"><?= $stats['teacherCount'] ?></h3>
                            <p class="mb-0">Teachers</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-info"><?= $stats['totalCourses'] ?></h3>
                            <p class="mb-0">Courses</p>
                        </div>
                    </div>
                </div>
            <?php elseif ($user['role'] === 'teacher'): ?>
                <!-- Teachers see their course stats and student counts -->
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-primary"><?= $stats['totalCourses'] ?></h3>
                            <p class="mb-0">My Courses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-success"><?= $stats['activeCourses'] ?></h3>
                            <p class="mb-0">Active Courses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-info"><?= $stats['totalStudents'] ?></h3>
                            <p class="mb-0">Total Students</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-warning"><?= isset($stats['pendingEnrollments']) ? $stats['pendingEnrollments'] : $stats['pendingReviews'] ?></h3>
                            <p class="mb-0"><?= isset($stats['pendingEnrollments']) ? 'Pending Enrollments' : 'Pending Reviews' ?></p>
                        </div>
                    </div>
                </div>
            <?php elseif ($user['role'] === 'student'): ?>
                <!-- Students see their academic progress - courses, grades, assignments -->
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-primary"><?= $stats['enrolledCourses'] ?></h3>
                            <p class="mb-0">Enrolled Courses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-success"><?= $stats['completedCourses'] ?></h3>
                            <p class="mb-0">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-info"><?= $stats['averageGrade'] ?></h3>
                            <p class="mb-0">Average GPA</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <h3 class="text-warning"><?= $stats['pendingAssignments'] ?></h3>
                            <p class="mb-0">Pending Tasks</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Main content area - shows different stuff based on role -->
        <?php if ($user['role'] === 'admin' && isset($recentUsers)): ?>
            <!-- Admin gets management tools and recent user list -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">Management Tools</h5>
                        </div>
                        <div class="card-body">
                            <!-- Quick access buttons for admin functions -->
                            <div class="d-grid gap-2">
                                <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-primary">
                                    Manage Users
                                </a>
                                <a href="<?= base_url('admin/courses') ?>" class="btn btn-outline-success">
                                    Manage Courses
                                </a>
                                <a href="<?= base_url('admin/reports') ?>" class="btn btn-outline-info">
                                    View Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Recent Users</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($recentUsers as $user): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong><?= esc($user['name']) ?></strong>
                                    <span class="badge bg-primary"><?= ucfirst($user['role']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($user['role'] === 'teacher' && isset($myCourses)): ?>
            <!-- Teachers see their courses and pending assignments to review -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">My Courses</h5>
                        </div>
                        <div class="card-body">
                            <!-- Show message if no courses exist -->
                            <?php if (empty($myCourses)): ?>
                                <div class="text-center py-4">
                                    <p class="text-muted mb-3">No courses created yet</p>
                                    <a href="<?= base_url('teacher/courses/create') ?>" class="btn btn-primary">Create Your First Course</a>
                                </div>
                            <?php else: ?>
                                <!-- Loop through all teacher's courses -->
                                <?php foreach ($myCourses as $course): ?>
                                    <div class="card mb-3 border-start border-warning border-3">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1"><?= esc($course['title']) ?></h6>
                                                    <?php if (!empty($course['description'])): ?>
                                                        <p class="text-muted mb-2 small"><?= esc(substr($course['description'], 0, 100)) ?><?= strlen($course['description']) > 100 ? '...' : '' ?></p>
                                                    <?php endif; ?>
                                                    <div class="d-flex gap-3">
                                                        <small class="text-muted">
                                                            <i class="fas fa-users"></i> <?= $course['students'] ?> students
                                                        </small>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar"></i> Created <?= date('M j, Y', strtotime($course['created_at'])) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <!-- Status badge - green for active, gray for completed -->
                                                    <span class="badge bg-<?= strtolower($course['status']) === 'active' ? 'success' : 'secondary' ?> mb-2">
                                                        <?= ucfirst($course['status']) ?>
                                                    </span>
                                                    <div>
                                                        <small class="text-muted d-block">Course ID: <?= $course['id'] ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Quick action buttons for teachers -->
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?= base_url('teacher/courses') ?>" class="btn btn-outline-primary">
                                    My Courses
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications for new assignment submissions -->
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">New Submissions</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($newSubmissions) && !empty($newSubmissions)): ?>
                                <?php foreach ($newSubmissions as $submission): ?>
                                    <div class="alert alert-info p-2 mb-2">
                                        <strong><?= esc($submission['student']) ?></strong> submitted<br>
                                        <small><?= esc($submission['assignment']) ?></small>
                                        <small class="text-muted d-block"><?= $submission['time'] ?> ago</small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center">No new submissions</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Pending Enrollment Requests -->
                    <?php if (isset($stats['pendingEnrollments']) && $stats['pendingEnrollments'] > 0): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="bi bi-person-plus"></i> Pending Enrollment Requests
                                <span class="badge bg-danger"><?= $stats['pendingEnrollments'] ?></span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                You have <?= $stats['pendingEnrollments'] ?> student(s) waiting for approval
                            </p>
                            <a href="<?= base_url('teacher/pending-enrollments') ?>" class="btn btn-warning w-100">
                                <i class="bi bi-eye"></i> Review Requests
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Pending reviews section -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Pending Reviews</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($pendingAssignments) && !empty($pendingAssignments)): ?>
                                <?php foreach ($pendingAssignments as $assignment): ?>
                                    <div class="mb-3">
                                        <h6><?= esc($assignment['student']) ?></h6>
                                        <small class="text-muted"><?= esc($assignment['assignment']) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center">No pending reviews</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($user['role'] === 'student'): ?>
            <!-- Students see their enrolled courses and available courses for enrollment -->
            <div class="row">
                <!-- Enrolled Courses Section (Left Column) -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">📚 Enrolled Courses</h5>
                            <button 
                                class="btn btn-light btn-sm" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#enrolledCoursesCollapse" 
                                aria-expanded="false" 
                                aria-controls="enrolledCoursesCollapse">
                                View My Courses
                            </button>
                        </div>
                        <div class="collapse" id="enrolledCoursesCollapse">
                            <div class="card-body">
                                <!-- Search Bar for Enrolled Courses -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" id="enrolledCoursesSearch" class="form-control" placeholder="Search enrolled courses...">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    </div>
                                </div>
                                
                                <!-- Show message if no courses enrolled -->
                                <?php if (empty($enrolledCourses)): ?>
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-3">No courses enrolled yet</p>
                                        <p class="text-sm">Browse available courses below to start learning!</p>
                                    </div>
                                <?php else: ?>
                                    <!-- Bootstrap list group for enrolled courses -->
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($enrolledCourses as $course): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between align-items-center">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1"><?= esc($course['title']) ?></h6>
                                                        <p class="mb-1 text-muted"><?= esc($course['description']) ?></p>
                                                        <small class="text-muted">Instructor: <?= esc($course['instructor_name']) ?></small>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-success">Enrolled</span>
                                                        <a href="<?= base_url('student/course/' . $course['course_id']) ?>" 
                                                           class="btn btn-outline-primary btn-sm">
                                                            View Course
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Available Courses Section (Right Column) -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">🎯 Available Courses</h5>
                            <button 
                                class="btn btn-light btn-sm" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#availableCoursesCollapse" 
                                aria-expanded="false" 
                                aria-controls="availableCoursesCollapse">
                                Check Available Courses
                            </button>
                        </div>
                        <div class="collapse" id="availableCoursesCollapse">
                            <div class="card-body">
                                <!-- Search Bar for Available Courses -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" id="availableCoursesSearch" class="form-control" placeholder="Search available courses...">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    </div>
                                </div>
                                
                                <!-- Show message if no available courses -->
                                <?php if (empty($availableCourses)): ?>
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-3">No courses available for enrollment</p>
                                        <p class="text-sm">Check back later for new courses!</p>
                                    </div>
                                <?php else: ?>
                                    <!-- Bootstrap list group for available courses -->
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($availableCourses as $course): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between align-items-center">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1"><?= esc($course['title']) ?></h6>
                                                        <p class="mb-1 text-muted"><?= esc($course['description']) ?></p>
                                                        <small class="text-muted">Instructor: <?= esc($course['instructor_name']) ?></small>
                                                    </div>
                                                    <div class="ms-3">
                                                        <!-- Enroll button with data attribute for AJAX -->
                                                        <button 
                                                            type="button" 
                                                            class="btn btn-primary btn-sm enroll-btn" 
                                                            data-course-id="<?= $course['id'] ?>"
                                                            data-course-title="<?= esc($course['title']) ?>">
                                                            Enroll
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info Section (Full Width) -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <!-- Deadlines section - helps students keep track of what's due -->
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">⏰ Upcoming Deadlines</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($upcomingDeadlines) && !empty($upcomingDeadlines)): ?>
                                <?php foreach ($upcomingDeadlines as $deadline): ?>
                                    <!-- Each deadline gets a warning alert box -->
                                    <div class="alert alert-warning p-2 mb-2">
                                        <strong><?= esc($deadline['assignment']) ?></strong><br>
                                        <small><?= esc($deadline['course']) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center">No upcoming deadlines</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Recent grades - shows the latest assignment scores -->
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">📊 Recent Grades</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($recentGrades) && !empty($recentGrades)): ?>
                                <?php foreach ($recentGrades as $grade): ?>
                                    <!-- Each grade shows assignment name, course, and the actual grade -->
                                    <div class="d-flex justify-content-between mb-2">
                                        <div>
                                            <h6 class="mb-1"><?= esc($grade['assignment']) ?></h6>
                                            <small class="text-muted"><?= esc($grade['course']) ?></small>
                                        </div>
                                        <span class="badge bg-success"><?= $grade['grade'] ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center">No grades available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="<?= csrf_token() ?>">

    <!-- jQuery for AJAX functionality -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS for dropdown menus and interactive stuff -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Notification System Script -->
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>

    <!-- Course Enrollment AJAX Script (Step 5 implementation) -->
    <script>
        $(document).ready(function() {
            // Get CSRF token from meta tag
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            // Handle enrollment button clicks
            $('.enroll-btn').on('click', function(e) {
                e.preventDefault(); // Prevent default form submission behavior
                
                const $button = $(this);
                const courseId = $button.data('course-id');
                const courseTitle = $button.data('course-title');
                
                // Disable button to prevent multiple clicks
                $button.prop('disabled', true).text('Enrolling...');
                
                // AJAX POST request to enrollment endpoint
                $.post('<?= base_url('course/enroll') ?>', {
                    course_id: courseId,
                    csrf_token: csrfToken
                })
                .done(function(response) {
                    // Success response from server
                    if (response.success) {
                        // Show success message using Bootstrap alert
                        showAlert('success', `Successfully enrolled in ${courseTitle}!`);
                        
                        // Hide or disable the enroll button for this course
                        $button.closest('.list-group-item').fadeOut(500, function() {
                            $(this).remove();
                            
                            // Check if no more available courses
                            if ($('.list-group .list-group-item').length === 0) {
                                $('.list-group').parent().html(`
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-3">No more courses available for enrollment</p>
                                        <p class="text-sm">Check back later for new courses!</p>
                                    </div>
                                `);
                            }
                        });
                        
                        // Add course to enrolled courses list dynamically
                        updateEnrolledCourses(response.data);
                        
                        // Update enrollment statistics
                        updateEnrollmentStats();
                        
                    } else {
                        // Server returned success=false
                        showAlert('warning', response.message || 'Enrollment failed. Please try again.');
                        $button.prop('disabled', false).text('Enroll');
                    }
                })
                .fail(function(xhr) {
                    // AJAX request failed
                    let errorMessage = 'An error occurred. Please try again.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    showAlert('danger', errorMessage);
                    $button.prop('disabled', false).text('Enroll');
                })
                .always(function() {
                    // This runs regardless of success or failure
                    console.log('Enrollment request completed');
                });
            });
            
            // Function to show Bootstrap alerts
            function showAlert(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                // Insert alert at the top of the container
                $('.container-fluid').prepend(alertHtml);
                
                // Auto-remove alert after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 5000);
            }
            
            // Function to update enrolled courses list dynamically
            function updateEnrolledCourses(courseData) {
                const $enrolledContainer = $('.card-header:contains("Enrolled Courses")').next('.card-body');
                const $enrolledList = $enrolledContainer.find('.list-group');
                
                // If no enrolled courses list exists, create it
                if ($enrolledList.length === 0) {
                    $enrolledContainer.html(`
                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <div>
                                        <h6 class="mb-1">${courseData.course_title}</h6>
                                        <p class="mb-1 text-muted">Recently enrolled</p>
                                        <small class="text-muted">Enrolled on: ${courseData.enrollment_date}</small>
                                    </div>
                                    <div>
                                        <span class="badge bg-success">Enrolled</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    // Add to existing list
                    $enrolledList.append(`
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <div>
                                    <h6 class="mb-1">${courseData.course_title}</h6>
                                    <p class="mb-1 text-muted">Recently enrolled</p>
                                    <small class="text-muted">Enrolled on: ${courseData.enrollment_date}</small>
                                </div>
                                <div>
                                    <span class="badge bg-success">Enrolled</span>
                                </div>
                            </div>
                        </div>
                    `);
                }
            }
            
            // Function to update enrollment statistics
            function updateEnrollmentStats() {
                const $statsCard = $('.card-body h3.text-primary').parent();
                const currentCount = parseInt($statsCard.find('h3').text());
                $statsCard.find('h3').text(currentCount + 1);
            }
            
            // Search functionality for Enrolled Courses
            $('#enrolledCoursesSearch').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#enrolledCoursesCollapse .list-group-item').each(function() {
                    const text = $(this).text().toLowerCase();
                    if (text.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
            
            // Search functionality for Available Courses
            $('#availableCoursesSearch').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#availableCoursesCollapse .list-group-item').each(function() {
                    const text = $(this).text().toLowerCase();
                    if (text.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
</body>
</html>