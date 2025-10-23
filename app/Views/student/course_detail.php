<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Course Detail - LMS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('student/dashboard') ?>">
                ITE311 FUNDAR LMS
            </a>
            
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="<?= base_url('student/dashboard') ?>">Dashboard</a>
                <a class="nav-link" href="<?= base_url('student/courses') ?>">My Courses</a>
            </div>
            
            <div class="navbar-nav">
                <span class="navbar-text">
                    <?= esc($user['name']) ?> 
                    <span class="badge bg-light text-dark">STUDENT</span>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Dashboard</li>
                <li class="breadcrumb-item">My Coursess</li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc($course['title']) ?></li>
            </ol>
        </nav>

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
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="card-title mb-2"><?= esc($course['title']) ?></h1>
                                <p class="card-text mb-2"><?= esc($course['description']) ?></p>
                                <p class="card-text">
                                    <strong>Instructor:</strong> <?= esc($course['instructor_name']) ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="badge bg-success fs-6 mb-2">Enrolled</span>
                                <br>
                                <small>Course Status: <?= ucfirst($course['status']) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Information -->
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">ℹ️ Additional Course Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <strong>Course Duration:</strong><br>
                                <span class="text-muted">Full Semester</span>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <strong>Course Level:</strong><br>
                                <span class="text-muted">Undergraduate</span>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <strong>Enrollment Date:</strong><br>
                                <span class="text-muted"><?= date('M j, Y', strtotime($course['created_at'])) ?></span>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <strong>Course ID:</strong><br>
                                <span class="text-muted"><?= $course['id'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>