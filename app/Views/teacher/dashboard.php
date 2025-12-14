<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Teacher Dashboard - LMS' ?></title>
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
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
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
            color: #ffc107;
        }
        
        .welcome-card p {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .teacher-badge {
            background-color: #ffc107;
            color: #000;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .stats-row {
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .text-primary { color: #0d6efd !important; }
        .text-success { color: #198754 !important; }
        .text-info { color: #0dcaf0 !important; }
        .text-warning { color: #ffc107 !important; }
        
        .management-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
        
        .course-management h3::before {
            content: "📚";
            color: #ffc107;
        }
        
        .enrollment-management h3::before {
            content: "👥";
            color: #ffc107;
        }
        
        .management-card p {
            color: #666;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .management-btn {
            background-color: white;
            border: 2px solid #ffc107;
            color: #ffc107;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .management-btn:hover {
            background-color: #ffc107;
            color: #000;
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
        
        .teacher-role-badge {
            background-color: #ffc107;
            color: #000;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        /* Sidebar Styles */
        .quick-actions, .new-submissions, .pending-reviews {
            background: white;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .quick-actions h3,
        .new-submissions h3,
        .pending-reviews h3 {
            color: #333;
            padding: 0.5rem 1rem;
            margin: -1rem -1rem 1rem -1rem;
            border-radius: 6px 6px 0 0;
            font-size: 1rem;
            font-weight: 600;
            background-color: white;
            border-bottom: 1px solid #ddd;
        }
        
        .action-item {
            padding: 0.5rem 0;
        }
        
        .action-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }
        
        .action-link:hover {
            text-decoration: underline;
        }
        
        .submissions-content, .reviews-content {
            color: #666;
            font-size: 0.9rem;
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
                <a class="navbar-brand" href="<?= base_url('teacher/dashboard') ?>">LMS</a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('teacher/dashboard') ?>">Dashboard</a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <!-- Notification Bell -->
                        <?php include(APPPATH . 'Views/partials/notification_bell.php'); ?>
                        
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
                Welcome Teacher User
            </div>
            
            <!-- Welcome Card -->
            <div class="welcome-card">
                <h1>Welcome, <?= esc($user['name']) ?>!</h1>
                <p>Manage your courses and students</p>
                <span class="teacher-badge">Teacher</span>
            </div>
            
            <!-- Statistics Cards Row -->
            <div class="row stats-row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-primary"><?= $stats['totalCourses'] ?? 0 ?></div>
                        <div class="stat-label">My Courses</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-success"><?= $stats['activeCourses'] ?? 0 ?></div>
                        <div class="stat-label">Active Courses</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-info"><?= $stats['totalStudents'] ?? 0 ?></div>
                        <div class="stat-label">Total Students</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-warning"><?= $stats['pendingEnrollments'] ?? 0 ?></div>
                        <div class="stat-label">Pending Enrollments</div>
                    </div>
                </div>
            </div>
            
            <!-- Management Cards -->
            <div class="management-cards">
                <div class="management-card course-management">
                    <h3>My Courses</h3>
                    <p>Manage your assigned courses and students.</p>
                    <a href="<?= base_url('teacher/courses') ?>" class="management-btn">View Courses</a>
                </div>
                
                <div class="management-card enrollment-management">
                    <h3>Pending Enrollments</h3>
                    <p>Review and approve student enrollment requests.</p>
                    <a href="<?= base_url('teacher/pending-enrollments') ?>" class="management-btn">View Requests</a>
                </div>
            </div>
            
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
                    <span class="teacher-role-badge">Teacher</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery and Notification Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
</body>
</html>