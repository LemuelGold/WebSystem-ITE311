<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Course Detail - LMS' ?></title>
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
            background-color: white !important;
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
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .course-title {
            color: #333;
            font-weight: 600;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .course-description {
            color: #666;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .course-meta {
            color: #666;
            font-size: 0.95rem;
        }
        
        .enrollment-badge {
            background-color: #d4edda;
            color: #155724;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .details-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        
        .details-card .card-header {
            background: white;
            border-bottom: 2px solid #000;
            padding: 1rem 1.5rem;
        }
        
        .details-card .card-header h6 {
            color: #333;
            font-weight: 600;
            margin: 0;
        }
        
        .detail-item {
            margin-bottom: 1rem;
        }
        
        .detail-label {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }
        
        .detail-value {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <span class="navbar-brand fw-bold" style="cursor: default;">
                LMS
            </span>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/courses') ?>">My Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/materials') ?>">Materials</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <!-- Notification Bell -->
                    <?= view('partials/notification_bell') ?>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= esc($user['name']) ?>
                            <span class="badge bg-dark ms-2">STUDENT</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
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

        <!-- Course Header -->
        <div class="course-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="course-title"><?= esc($course['title']) ?></h1>
                    <p class="course-description"><?= esc($course['description']) ?></p>
                    <p class="course-meta">
                        <i class="bi bi-person"></i> <strong>Instructor:</strong> <?= esc($course['instructor_name']) ?>
                        <?php if (!empty($course['units'])): ?>
                            <br><i class="bi bi-award"></i> <strong>Units:</strong> <?= esc($course['units']) ?> Unit<?= $course['units'] > 1 ? 's' : '' ?>
                        <?php endif; ?>
                        <?php if (!empty($course['term'])): ?>
                            <br><i class="bi bi-calendar-range"></i> <strong>Term:</strong> <?= esc($course['term']) ?>
                        <?php endif; ?>
                        <?php if (!empty($course['academic_year'])): ?>
                            <br><i class="bi bi-calendar"></i> <strong>Academic Year:</strong> <?= esc($course['academic_year']) ?> - <?= esc($course['academic_year'] + 1) ?>
                        <?php endif; ?>
                        <?php if (!empty($course['semester'])): ?>
                            <br><i class="bi bi-calendar-event"></i> <strong>Semester:</strong> <?= esc($course['semester']) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="enrollment-badge">
                        <i class="bi bi-check-circle"></i> Enrolled
                    </span>
                    <br><br>
                    <small class="text-muted">Course Status: <?= ucfirst($course['status']) ?></small>
                </div>
            </div>
        </div>

        <!-- Course Information -->
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="details-card">
                    <div class="card-header">
                        <h6><i class="bi bi-info-circle"></i> Additional Course Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="detail-item">
                                    <div class="detail-label">Course Duration:</div>
                                    <div class="detail-value">Full Semester</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-item">
                                    <div class="detail-label">Course Level:</div>
                                    <div class="detail-value">Undergraduate</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-item">
                                    <div class="detail-label">Enrollment Date:</div>
                                    <div class="detail-value">
                                        <?php 
                                        $enrollmentDate = $course['enrollment_date'] ?? $course['enrollment_created_at'] ?? null;
                                        if ($enrollmentDate && $enrollmentDate != '0000-00-00 00:00:00') {
                                            echo date('M j, Y', strtotime($enrollmentDate));
                                        } else {
                                            echo 'Not enrolled';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-item">
                                    <div class="detail-label">Course ID:</div>
                                    <div class="detail-value"><?= $course['id'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
</body>
</html>