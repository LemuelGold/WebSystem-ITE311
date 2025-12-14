<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard - LMS' ?></title>
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
            margin: 0;
            padding: 0;
        }
        
        .navbar {
            background-color: white;
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
        
        .navbar-nav .nav-link:hover {
            color: #333 !important;
        }
        
        .welcome-banner {
            background-color: #d4edda;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 5px;
            color: #155724;
            font-weight: 500;
        }
        
        .welcome-card {
            background-color: white;
            border: 2px solid #000;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .welcome-card h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .welcome-card h1::before {
            content: "👤";
            margin-right: 10px;
            color: #dc3545;
        }
        
        .welcome-card p {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .admin-badge {
            background-color: #dc3545;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .teacher-badge {
            background-color: #ffc107;
            color: #000;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .student-badge {
            background-color: #0d6efd;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .management-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .management-card {
            background-color: white;
            border: 2px solid #000;
            border-radius: 10px;
            padding: 1.5rem;
        }
        
        .management-card h3 {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .management-card h3::before {
            margin-right: 8px;
        }
        
        .user-management h3::before {
            content: "👥";
            color: #dc3545;
        }
        
        .course-management h3::before {
            content: "📚";
            color: #dc3545;
        }
        
        .program-management h3::before {
            content: "🎓";
            color: #dc3545;
        }
        
        .management-card p {
            color: #666;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .management-btn {
            background-color: white;
            border: 2px solid #dc3545;
            color: #dc3545;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .management-btn:hover {
            background-color: #dc3545;
            color: white;
        }
        
        .account-info {
            background-color: white;
            border: 2px solid #000;
            border-radius: 10px;
            padding: 1.5rem;
        }
        
        .account-info h3 {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .account-info h3::before {
            content: "👤";
            margin-right: 8px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #333;
        }
        
        .info-value {
            color: #666;
        }
        
        .admin-role-badge {
            background-color: #dc3545;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .management-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <span class="navbar-brand" style="cursor: default;">LMS</span>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <?php 
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
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= $dashboardUrl ?>">Dashboard</a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <!-- Notification Bell -->
                        <?= view('partials/notification_bell') ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('logout') ?>">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-4">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                Welcome <?= ucfirst($user['role']) ?> User
            </div>
            
            <!-- Welcome Card -->
            <div class="welcome-card">
                <h1>Welcome, <?= esc($user['name']) ?>!</h1>
                <?php if ($user['role'] === 'admin'): ?>
                    <p>Manage the entire learning management system</p>
                    <span class="admin-badge">Admin</span>
                <?php elseif ($user['role'] === 'teacher'): ?>
                    <p>Manage your courses and students</p>
                    <span class="teacher-badge">Teacher</span>
                <?php else: ?>
                    <p>Access your enrolled courses and materials</p>
                    <span class="student-badge">Student</span>
                    <?php if (isset($studentProgram) && $studentProgram): ?>
                        <div class="program-info mt-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2"><?= esc($studentProgram['program_code']) ?></span>
                                <span class="text-muted"><?= esc($studentProgram['program_name']) ?></span>
                            </div>
                            <small class="text-muted">
                                Year <?= $studentProgram['current_year_level'] ?> - <?= $studentProgram['current_semester'] ?> 
                                | <?= $studentProgram['academic_year'] ?>
                                <?php if ($studentProgram['student_number']): ?>
                                    | ID: <?= esc($studentProgram['student_number']) ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mt-3" role="alert">
                            <small><i class="bi bi-exclamation-triangle"></i> You are not enrolled in any program yet. Please contact the administrator.</small>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <!-- Management Cards -->
            <?php if ($user['role'] === 'admin'): ?>
            <div class="management-cards">
                <div class="management-card user-management">
                    <h3>User Management</h3>
                    <p>Manage all users, roles, and permissions in the system.</p>
                    <a href="<?= base_url('admin/users') ?>" class="management-btn">Manage Users</a>
                </div>
                
                <div class="management-card course-management">
                    <h3>Course Management</h3>
                    <p>Create, edit, and manage all courses in the system.</p>
                    <a href="<?= base_url('admin/courses') ?>" class="management-btn">Manage Courses</a>
                </div>
                
                <div class="management-card program-management">
                    <h3>Program Management</h3>
                    <p>Create and manage academic programs and curricula.</p>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('admin/programs') ?>" class="management-btn">Manage Programs</a>
                        <a href="<?= base_url('admin/student-programs') ?>" class="management-btn">Student Enrollments</a>
                    </div>
                </div>
            </div>
            <?php elseif ($user['role'] === 'teacher'): ?>
            <div class="management-cards">
                <div class="management-card course-management">
                    <h3>My Courses</h3>
                    <p>Manage your assigned courses and students.</p>
                    <a href="<?= base_url('teacher/courses') ?>" class="management-btn">View Courses</a>
                </div>
                
                <div class="management-card user-management">
                    <h3>Pending Enrollments</h3>
                    <p>Review and approve student enrollment requests.</p>
                    <a href="<?= base_url('teacher/enrollments') ?>" class="management-btn">View Requests</a>
                </div>
            </div>
            <?php else: ?>
            
            <!-- Approved Enrollments Waiting for Confirmation -->
            <?php if (!empty($approvedEnrollments)): ?>
                <div class="alert alert-info" role="alert">
                    <h5 class="alert-heading"><i class="bi bi-check-circle"></i> Enrollment Approvals</h5>
                    <p>You have <?= count($approvedEnrollments) ?> course enrollment(s) waiting for your confirmation. Please accept or decline below:</p>
                    
                    <?php foreach ($approvedEnrollments as $enrollment): ?>
                        <div class="card mb-3" style="border: 2px solid #0dcaf0;">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="card-title mb-1"><?= esc($enrollment['title']) ?></h6>
                                        <p class="card-text mb-1">
                                            <small class="text-muted">
                                                Instructor: <?= esc($enrollment['instructor_name'] ?? 'Unassigned') ?>
                                                <?php if (!empty($enrollment['units'])): ?>
                                                    | Units: <?= esc($enrollment['units']) ?>
                                                <?php endif; ?>
                                                <?php if (!empty($enrollment['term'])): ?>
                                                    | Term: <?= esc($enrollment['term']) ?>
                                                <?php endif; ?>
                                            </small>
                                        </p>
                                        <p class="card-text mb-0">
                                            <small class="<?= $enrollment['status'] === 'approved' ? 'text-success' : 'text-info' ?>">
                                                <i class="bi bi-<?= $enrollment['status'] === 'approved' ? 'check-lg' : 'clock' ?>"></i> 
                                                <?= ucfirst($enrollment['status']) ?> on <?= date('M d, Y', strtotime($enrollment['updated_at'])) ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <button class="btn btn-success btn-sm me-2" 
                                                onclick="acceptEnrollment(<?= $enrollment['id'] ?>, '<?= esc($enrollment['title']) ?>')">
                                            <i class="bi bi-check-lg"></i> Accept
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="declineEnrollment(<?= $enrollment['id'] ?>, '<?= esc($enrollment['title']) ?>')">
                                            <i class="bi bi-x-lg"></i> Decline
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="management-cards">
                <div class="management-card course-management">
                    <h3>My Courses</h3>
                    <p>Access your enrolled courses and materials.</p>
                    <a href="<?= base_url('student/courses') ?>" class="management-btn">View Courses</a>
                </div>
                
                <div class="management-card user-management">
                    <h3>Available Courses</h3>
                    <?php if (isset($studentProgram) && $studentProgram): ?>
                        <p>Browse courses from your <?= esc($studentProgram['program_code']) ?> program curriculum.</p>
                        <?php if (isset($availableCourses) && !empty($availableCourses)): ?>
                            <small class="text-success"><i class="bi bi-check-circle"></i> <?= count($availableCourses) ?> courses available for enrollment</small>
                        <?php else: ?>
                            <small class="text-muted"><i class="bi bi-info-circle"></i> No new courses available at this time</small>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>You must be enrolled in a program to view available courses.</p>
                    <?php endif; ?>
                    <a href="<?= base_url('courses') ?>" class="management-btn">Browse Courses</a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Account Information -->
            <div class="account-info">
                <h3>Account Information</h3>
                <div class="info-row">
                    <span class="info-label">Full Name</span>
                    <span class="info-value"><?= esc($user['name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?= esc($user['email']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Role</span>
                    <span class="admin-role-badge"><?= ucfirst($user['role']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
    
    <script>
        // Student enrollment confirmation functions
        function acceptEnrollment(enrollmentId, courseTitle) {
            if (confirm(`Are you sure you want to accept enrollment in "${courseTitle}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url("student/enrollment/accept") ?>';
                
                const enrollmentIdInput = document.createElement('input');
                enrollmentIdInput.type = 'hidden';
                enrollmentIdInput.name = 'enrollment_id';
                enrollmentIdInput.value = enrollmentId;
                
                form.appendChild(enrollmentIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function declineEnrollment(enrollmentId, courseTitle) {
            if (confirm(`Are you sure you want to decline enrollment in "${courseTitle}"? This action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url("student/enrollment/decline") ?>';
                
                const enrollmentIdInput = document.createElement('input');
                enrollmentIdInput.type = 'hidden';
                enrollmentIdInput.name = 'enrollment_id';
                enrollmentIdInput.value = enrollmentId;
                
                form.appendChild(enrollmentIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>