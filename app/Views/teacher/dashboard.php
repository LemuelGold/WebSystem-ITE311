<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Teacher Dashboard - LMS' ?></title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Teacher dashboard page -->
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
        <div class="container">
            <a class="navbar-brand text-dark" href="<?= base_url('teacher/dashboard') ?>">LMS - Teacher</a>
            <div class="navbar-nav ms-auto">
                <!-- Display logged-in teacher's name -->
                <span class="navbar-text text-dark">
                    <?= esc($user['name']) ?> (Teacher)
                </span>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <?php if (session()->getFlashdata('success')): ?>
            <!-- Success message display -->
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <!-- Error message display -->
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Welcome -->
        <div class="row mb-4">
            <div class="col-12">
                <!-- Welcome message card -->
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h2>Teacher Dashboard - Welcome, <?= esc($user['name']) ?>!</h2>
                        <p>Manage your courses and track student progress</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary"><?= $stats['totalCourses'] ?></h3>
                        <p>My Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success"><?= $stats['activeCourses'] ?></h3>
                        <p>Active Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info"><?= $stats['totalStudents'] ?></h3>
                        <p>Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning"><?= $stats['pendingReviews'] ?></h3>
                        <p>Pending Reviews</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Actions and Course List -->
        <div class="row mb-4">
            <div class="col-md-6">
                <!-- Teacher management options -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Teaching Tools</h5>
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('teacher/courses') ?>" class="btn btn-outline-primary">My Courses</a>
                            <a href="<?= base_url('teacher/assignments') ?>" class="btn btn-outline-success">Assignments</a>
                            <a href="<?= base_url('teacher/students') ?>" class="btn btn-outline-info">View Students</a>
                            <a href="<?= base_url('teacher/courses/create') ?>" class="btn btn-outline-secondary">Create New Course</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Course list -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">My Courses</h5>
                        <?php if (!empty($myCourses)): ?>
                            <?php foreach (array_slice($myCourses, 0, 3) as $course): ?>
                                <div class="mb-2">
                                    <strong><?= esc($course['name']) ?></strong>
                                    <span class="badge bg-<?= $course['status'] === 'Active' ? 'success' : 'secondary' ?> ms-2">
                                        <?= $course['status'] ?>
                                    </span>
                                    <br>
                                    <small class="text-muted"><?= $course['students'] ?> students enrolled</small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No courses assigned yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pending Assignment Reviews</h5>
                        <?php if (!empty($pendingAssignments)): ?>
                            <div class="row">
                                <?php foreach ($pendingAssignments as $assignment): ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="card border-warning">
                                            <div class="card-body p-3">
                                                <h6><?= esc($assignment['student']) ?></h6>
                                                <p class="mb-1"><?= esc($assignment['assignment']) ?></p>
                                                <small class="text-muted"><?= esc($assignment['course']) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No pending reviews.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="position-fixed bottom-0 start-0 p-3">
            <!-- Fixed logout button in bottom-left corner -->
            <a href="<?= base_url('logout') ?>" class="btn btn-danger" 
               onclick="return confirm('Logout?')">Logout</a>
        </div>
    </div>

    <!-- Bootstrap JavaScript for interactive components -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<body>
    <!-- Navigation Bar with teacher-specific menu -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="<?= base_url() ?>">
                <i class="fas fa-graduation-cap me-2"></i>ITE311 FUNDAR LMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('teacher/dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('teacher/courses') ?>">
                            <i class="fas fa-book me-1"></i>My Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('teacher/assignments') ?>">
                            <i class="fas fa-tasks me-1"></i>Assignments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('teacher/students') ?>">
                            <i class="fas fa-user-graduate me-1"></i>Students
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?= esc($user['name']) ?>
                            <span class="teacher-badge ms-2">TEACHER</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('profile') ?>">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="container">
            <!-- Welcome Message -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h1 class="card-title mb-0">
                                <i class="fas fa-chalkboard-teacher text-success me-2"></i>
                                Welcome back, <?= esc($user['name']) ?>!
                            </h1>
                            <p class="card-text text-muted">Manage your courses, track student progress, and create engaging content.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="fas fa-book fa-2x mb-3"></i>
                            <div class="stat-number"><?= $stats['totalCourses'] ?></div>
                            <div>Total Courses</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="fas fa-play fa-2x mb-3"></i>
                            <div class="stat-number"><?= $stats['activeCourses'] ?></div>
                            <div>Active Courses</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="fas fa-user-graduate fa-2x mb-3"></i>
                            <div class="stat-number"><?= $stats['totalStudents'] ?></div>
                            <div>Total Students</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x mb-3"></i>
                            <div class="stat-number"><?= $stats['pendingReviews'] ?></div>
                            <div>Pending Reviews</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- My Courses -->
                <div class="col-md-8 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-book me-2"></i>My Courses</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($myCourses)): ?>
                                <?php foreach ($myCourses as $course): ?>
                                    <div class="card course-card mb-3">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="card-title mb-1"><?= esc($course['name']) ?></h6>
                                                    <p class="card-text text-muted mb-2">
                                                        <i class="fas fa-users me-1"></i><?= $course['students'] ?> Students
                                                    </p>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <span class="badge bg-<?= $course['status'] === 'Active' ? 'success' : 'secondary' ?> mb-2">
                                                        <?= $course['status'] ?>
                                                    </span>
                                                    <br>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= base_url('teacher/course/' . $course['id']) ?>" class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('teacher/course/' . $course['id'] . '/edit') ?>" class="btn btn-outline-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No courses assigned yet.</p>
                            <?php endif; ?>
                            
                            <div class="text-center">
                                <a href="<?= base_url('teacher/courses/create') ?>" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Create New Course
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Pending Assignments -->
                <div class="col-md-4">
                    <!-- Quick Actions -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?= base_url('teacher/assignments/create') ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i>New Assignment
                                </a>
                                <a href="<?= base_url('teacher/announcements') ?>" class="btn btn-outline-info">
                                    <i class="fas fa-bullhorn me-2"></i>Announcements
                                </a>
                                <a href="<?= base_url('teacher/gradebook') ?>" class="btn btn-outline-success">
                                    <i class="fas fa-chart-line me-2"></i>Gradebook
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Assignments -->
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Pending Reviews</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($pendingAssignments)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($pendingAssignments as $assignment): ?>
                                        <div class="list-group-item px-0">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?= esc($assignment['student']) ?></h6>
                                                <small><?= date('M j', strtotime($assignment['submitted'])) ?></small>
                                            </div>
                                            <p class="mb-1 text-muted"><?= esc($assignment['assignment']) ?></p>
                                            <small class="text-muted"><?= esc($assignment['course']) ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="<?= base_url('teacher/reviews') ?>" class="btn btn-warning btn-sm">
                                        View All Reviews
                                    </a>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No pending reviews.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teaching Analytics -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Teaching Analytics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <h6>Course Completion</h6>
                                    <div class="progress mb-3" style="height: 25px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 75%">
                                            75%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h6>Student Engagement</h6>
                                    <div class="progress mb-3" style="height: 25px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 85%">
                                            85%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h6>Assignment Submissions</h6>
                                    <div class="progress mb-3" style="height: 25px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 92%">
                                            92%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h6>Class Average</h6>
                                    <div class="text-center">
                                        <h3 class="text-success">B+</h3>
                                        <small class="text-muted">3.3 GPA</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>