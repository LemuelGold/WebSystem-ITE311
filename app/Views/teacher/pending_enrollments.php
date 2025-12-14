<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pending Enrollment Requests' ?></title>
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
        
        .page-header {
            background: white;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .page-header h2 {
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .page-header h2::before {
            content: "👥";
            margin-right: 8px;
        }
        
        .page-header p {
            color: #666;
            margin-bottom: 0;
        }
        
        .pending-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .pending-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .no-requests-card {
            background: white;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 3rem;
            text-align: center;
        }
        
        .no-requests-card h4 {
            color: #333;
            margin-bottom: 1rem;
        }
        
        .no-requests-card p {
            color: #666;
        }
        
        .alert {
            border-radius: 8px;
        }
        
        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }
        
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('teacher/dashboard') ?>">LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('teacher/dashboard') ?>">Dashboard</a>
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

    <div class="container mt-4 mb-5">
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

        <!-- Page header -->
        <div class="page-header">
            <h2>Pending Enrollment Requests</h2>
            <p>Waiting for students to accept your enrollment invitations</p>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <?php if (empty($pendingEnrollments)): ?>
                    <div class="no-requests-card">
                        <i class="bi bi-clock-history text-info" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">No Pending Student Responses</h4>
                        <p>All students have responded to your enrollment invitations.</p>
                        <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-primary mt-3">
                            Back to Dashboard
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($pendingEnrollments as $enrollment): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card pending-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div>
                                                        <h5 class="card-title mb-1">
                                                            <i class="bi bi-person-circle text-primary"></i>
                                                            <?= esc($enrollment['student_name']) ?>
                                                        </h5>
                                                        <p class="text-muted mb-0 small">
                                                            <i class="bi bi-envelope"></i> <?= esc($enrollment['student_email']) ?>
                                                        </p>
                                                    </div>
                                                    <span class="badge bg-info text-white">Waiting for Response</span>
                                                </div>

                                                <div class="mb-3">
                                                    <h6 class="text-muted mb-1">Course:</h6>
                                                    <p class="mb-0">
                                                        <i class="bi bi-book"></i> <strong><?= esc($enrollment['course_title']) ?></strong>
                                                    </p>
                                                </div>

                                                <div class="mb-3">
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock"></i> 
                                                        Invitation sent: <?= date('M d, Y h:i A', strtotime($enrollment['created_at'])) ?>
                                                    </small>
                                                </div>

                                                <div class="alert alert-info mb-0 py-2">
                                                    <i class="bi bi-hourglass-split"></i>
                                                    <small>Waiting for student to accept invitation</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery and Notification Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
</body>
</html>
