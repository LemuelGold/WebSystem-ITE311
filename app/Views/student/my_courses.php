<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Courses - LMS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('student/dashboard') ?>">
                ITE311 FUNDAR LMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('student/courses') ?>">My Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/materials') ?>">Materials</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text text-dark">
                            <?= esc($user['name']) ?>
                            <span class="badge bg-dark ms-2">STUDENT</span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-info text-dark">
                    <div class="card-body">
                        <h2 class="mb-0">📚 My Courses</h2>
                        <p class="mb-0">View and access all your enrolled courses</p>
                    </div>
                </div>
            </div>
        </div>

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

        <!-- Enrolled Courses -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Enrolled Courses (<?= count($enrolledCourses) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($enrolledCourses)): ?>
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-book-open fa-4x text-muted"></i>
                                </div>
                                <h4 class="text-muted">No Courses Enrolled</h4>
                                <p class="text-muted mb-4">You haven't enrolled in any courses yet.</p>
                                <a href="<?= base_url('student/dashboard') ?>" class="btn btn-primary">
                                    Browse Available Courses
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($enrolledCourses as $course): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                                <p class="card-text text-muted"><?= esc($course['description']) ?></p>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <strong>Instructor:</strong> <?= esc($course['instructor_name']) ?><br>
                                                        <strong>Enrolled:</strong> <?= date('M j, Y', strtotime($course['enrollment_date'])) ?>
                                                    </small>
                                                </p>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-success">Enrolled</span>
                                                    <a href="<?= base_url('student/course/' . $course['course_id']) ?>" 
                                                       class="btn btn-primary btn-sm">
                                                        View Course
                                                    </a>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>