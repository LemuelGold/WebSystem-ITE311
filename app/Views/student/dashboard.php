    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Student Dashboard - LMS' ?></title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Student dashboard page -->
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('student/dashboard') ?>">LMS - Student</a>
            <div class="navbar-nav ms-auto">
                <!-- Display logged-in student's name -->
                <span class="navbar-text text-white">
                    <?= esc($user['name']) ?> (Student)
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
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h2>Student Dashboard - Welcome, <?= esc($user['name']) ?>!</h2>
                        <p>Track your courses, assignments, and academic progress</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary"><?= $stats['enrolledCourses'] ?></h3>
                        <p>Enrolled Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success"><?= $stats['completedCourses'] ?></h3>
                        <p>Completed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info"><?= $stats['averageGrade'] ?></h3>
                        <p>Average GPA</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning"><?= $stats['pendingAssignments'] ?></h3>
                        <p>Pending Tasks</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Actions and Course List -->
        <div class="row mb-4">
            <div class="col-md-6">
                <!-- Student navigation options -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Access</h5>
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('student/courses') ?>" class="btn btn-outline-primary">My Courses</a>
                            <a href="<?= base_url('student/assignments') ?>" class="btn btn-outline-success">Assignments</a>
                            <a href="<?= base_url('student/grades') ?>" class="btn btn-outline-info">View Grades</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Course progress -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Course Progress</h5>
                        <?php if (!empty($enrolledCourses)): ?>
                            <?php foreach (array_slice($enrolledCourses, 0, 3) as $course): ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= esc($course['name']) ?></strong>
                                        <span class="badge bg-<?= 
                                            strpos($course['grade'], 'A') === 0 ? 'success' : 
                                            (strpos($course['grade'], 'B') === 0 ? 'primary' : 'warning')
                                        ?>"><?= $course['grade'] ?></span>
                                    </div>
                                    <div class="progress mt-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: <?= $course['progress'] ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?= $course['progress'] ?>% complete</small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No courses enrolled yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Deadlines and Recent Grades -->
        <div class="row mb-4">
            <div class="col-md-6">
                <!-- Upcoming deadlines -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Upcoming Deadlines</h5>
                        <?php if (!empty($upcomingDeadlines)): ?>
                            <?php foreach (array_slice($upcomingDeadlines, 0, 3) as $deadline): ?>
                                <?php 
                                    $daysLeft = ceil((strtotime($deadline['due_date']) - time()) / (60 * 60 * 24));
                                    $urgencyClass = $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'success');
                                ?>
                                <div class="alert alert-<?= $urgencyClass ?> p-2 mb-2">
                                    <strong><?= esc($deadline['assignment']) ?></strong><br>
                                    <small><?= esc($deadline['course']) ?> - Due: <?= date('M j', strtotime($deadline['due_date'])) ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No upcoming deadlines.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Recent grades -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Grades</h5>
                        <?php if (!empty($recentGrades)): ?>
                            <?php foreach ($recentGrades as $grade): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong><?= esc($grade['assignment']) ?></strong><br>
                                        <small class="text-muted"><?= esc($grade['course']) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-<?= 
                                            strpos($grade['grade'], 'A') === 0 ? 'success' : 
                                            (strpos($grade['grade'], 'B') === 0 ? 'primary' : 'warning')
                                        ?>"><?= $grade['grade'] ?></span><br>
                                        <small class="text-muted"><?= date('M j', strtotime($grade['date'])) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No recent grades available.</p>
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
    <!-- Navigation Bar with student-specific menu -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="<?= base_url() ?>">
                <i class="fas fa-graduation-cap me-2"></i>ITE311 FUNDAR LMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('student/dashboard') ?>">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/courses') ?>">
                            <i class="fas fa-book me-1"></i>My Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/assignments') ?>">
                            <i class="fas fa-tasks me-1"></i>Assignments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/grades') ?>">
                            <i class="fas fa-chart-line me-1"></i>Grades
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?= esc($user['name']) ?>
                            <span class="student-badge ms-2">STUDENT</span>
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
                                <i class="fas fa-user-graduate text-primary me-2"></i>
                                Welcome back, <?= esc($user['name']) ?>!
                            </h1>
                            <p class="card-text text-muted">Continue your learning journey and track your academic progress.</p>
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
                            <div class="stat-number"><?= $stats['enrolledCourses'] ?></div>
                            <div>Enrolled Courses</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x mb-3"></i>
                            <div class="stat-number"><?= $stats['completedCourses'] ?></div>
                            <div>Completed Courses</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="fas fa-star fa-2x mb-3"></i>
                            <div class="stat-number"><?= $stats['averageGrade'] ?></div>
                            <div>Average GPA</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x mb-3"></i>
                            <div class="stat-number"><?= $stats['pendingAssignments'] ?></div>
                            <div>Pending Tasks</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Enrolled Courses -->
                <div class="col-md-8 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-book me-2"></i>My Courses</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($enrolledCourses)): ?>
                                <?php foreach ($enrolledCourses as $course): ?>
                                    <div class="card course-progress mb-3">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <h6 class="card-title mb-1"><?= esc($course['name']) ?></h6>
                                                    <p class="card-text text-muted mb-2">
                                                        <i class="fas fa-chalkboard-teacher me-1"></i><?= esc($course['teacher']) ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="progress mb-2" style="height: 8px;">
                                                        <div class="progress-bar bg-success" 
                                                             style="width: <?= $course['progress'] ?>%"></div>
                                                    </div>
                                                    <small class="text-muted"><?= $course['progress'] ?>% Complete</small>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <span class="badge grade-badge bg-<?= 
                                                        strpos($course['grade'], 'A') === 0 ? 'success' : 
                                                        (strpos($course['grade'], 'B') === 0 ? 'primary' : 
                                                        (strpos($course['grade'], 'C') === 0 ? 'warning' : 'secondary'))
                                                    ?>">
                                                        <?= $course['grade'] ?>
                                                    </span>
                                                    <br>
                                                    <a href="<?= base_url('student/course/' . $course['id']) ?>" 
                                                       class="btn btn-outline-primary btn-sm mt-2">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No courses enrolled yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Upcoming Deadlines -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Deadlines</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($upcomingDeadlines)): ?>
                                <?php foreach ($upcomingDeadlines as $deadline): ?>
                                    <?php 
                                        $daysLeft = ceil((strtotime($deadline['due_date']) - time()) / (60 * 60 * 24));
                                        $urgencyClass = $daysLeft <= 1 ? 'deadline-urgent' : ($daysLeft <= 3 ? 'deadline-warning' : 'deadline-normal');
                                    ?>
                                    <div class="card <?= $urgencyClass ?> mb-2">
                                        <div class="card-body py-2">
                                            <h6 class="mb-1"><?= esc($deadline['assignment']) ?></h6>
                                            <p class="mb-1 text-muted small"><?= esc($deadline['course']) ?></p>
                                            <small class="text-<?= $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'success') ?>">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= $daysLeft <= 0 ? 'Due today' : $daysLeft . ' days left' ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No upcoming deadlines.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Grades -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Recent Grades</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recentGrades)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recentGrades as $grade): ?>
                                        <div class="list-group-item px-0">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1"><?= esc($grade['assignment']) ?></h6>
                                                    <small class="text-muted"><?= esc($grade['course']) ?></small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-<?= 
                                                        strpos($grade['grade'], 'A') === 0 ? 'success' : 
                                                        (strpos($grade['grade'], 'B') === 0 ? 'primary' : 
                                                        (strpos($grade['grade'], 'C') === 0 ? 'warning' : 'secondary'))
                                                    ?>"><?= $grade['grade'] ?></span>
                                                    <br>
                                                    <small class="text-muted"><?= date('M j', strtotime($grade['date'])) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No recent grades available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Progress -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Academic Progress</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <h6>Overall Progress</h6>
                                    <div class="progress mb-3" style="height: 25px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 68%">
                                            68%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h6>Assignment Completion</h6>
                                    <div class="progress mb-3" style="height: 25px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 85%">
                                            85%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h6>Attendance Rate</h6>
                                    <div class="progress mb-3" style="height: 25px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 92%">
                                            92%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h6>Current Semester</h6>
                                    <div class="text-center">
                                        <h3 class="text-primary">3.2</h3>
                                        <small class="text-muted">GPA</small>
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