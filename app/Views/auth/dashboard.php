<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard - LMS' ?></title>
    <!-- Just using Bootstrap for styling - much easier than writing custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        /* Simple hover effect for the stat cards - makes it feel more interactive */
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-light role-<?= $user['role'] ?>">
    <!-- I made this navbar dynamic - it shows different links depending on who's logged in -->
    <nav class="navbar navbar-expand-lg navbar-dark">
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
                        <a class="nav-link active" href="<?= base_url('dashboard') ?>">
                            Dashboard
                        </a>
                    </li>
                    <?php if ($user['role'] === 'admin'): ?>
                        <!-- Admin navigation - keep it simple, main tools are in dashboard cards -->
                    <?php elseif ($user['role'] === 'teacher'): ?>
                        <!-- Teacher navigation - keep it simple, main tools are in dashboard cards -->
                    <?php elseif ($user['role'] === 'student'): ?>
                        <!-- Student navigation - keep it simple, main tools are in dashboard cards -->
                    <?php endif; ?>
                </ul>
                
                <!-- User menu on the right side - shows name and role badge -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= esc($user['name']) ?>
                            <!-- This badge shows the user's role - pretty neat visual cue -->
                            <span class="badge bg-light text-dark ms-2"><?= strtoupper($user['role']) ?></span>
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

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
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
                            <h3 class="text-warning"><?= $stats['pendingReviews'] ?></h3>
                            <p class="mb-0">Pending Reviews</p>
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
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6><?= esc($course['name']) ?></h6>
                                                    <small class="text-muted"><?= $course['students'] ?> students</small>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <!-- Status badge - green for active, gray for completed -->
                                                    <span class="badge bg-<?= $course['status'] === 'Active' ? 'success' : 'secondary' ?>">
                                                        <?= $course['status'] ?>
                                                    </span>
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
                                <a href="<?= base_url('teacher/courses/create') ?>" class="btn btn-outline-primary">
                                    Create New Course
                                </a>
                                <a href="<?= base_url('teacher/lessons/create') ?>" class="btn btn-outline-success">
                                    Create New Lesson
                                </a>
                                <a href="<?= base_url('teacher/assignments/create') ?>" class="btn btn-outline-info">
                                    Create Assignment
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

        <?php elseif ($user['role'] === 'student' && isset($enrolledCourses)): ?>
            <!-- Students see their enrolled courses with progress bars and grades -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">My Courses</h5>
                        </div>
                        <div class="card-body">
                            <!-- Show message if no courses enrolled -->
                            <?php if (empty($enrolledCourses)): ?>
                                <div class="text-center py-4">
                                    <p class="text-muted mb-3">No courses enrolled yet</p>
                                    <a href="<?= base_url('student/courses') ?>" class="btn btn-primary">Browse Available Courses</a>
                                </div>
                            <?php else: ?>
                                <!-- Show each course the student is enrolled in -->
                                <?php foreach ($enrolledCourses as $course): ?>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <h6><?= esc($course['name']) ?></h6>
                                                    <small class="text-muted"><?= esc($course['teacher']) ?></small>
                                                </div>
                                                <div class="col-md-3">
                                                    <!-- Progress bar showing course completion -->
                                                    <div class="progress mb-1">
                                                        <div class="progress-bar" style="width: <?= $course['progress'] ?>%"></div>
                                                    </div>
                                                    <small><?= $course['progress'] ?>% Complete</small>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <!-- Current grade badge -->
                                                    <span class="badge bg-success"><?= $course['grade'] ?></span>
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
                    <!-- Deadlines section - helps students keep track of what's due -->
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">Upcoming Deadlines</h5>
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
                    
                    <!-- Recent grades - shows the latest assignment scores -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Recent Grades</h5>
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

    <!-- Bootstrap JS for dropdown menus and interactive stuff -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>