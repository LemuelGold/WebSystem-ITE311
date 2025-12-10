<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Courses - Teacher Panel' ?></title>
    <!-- Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .course-card {
            transition: transform 0.2s;
        }
        .course-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-warning">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="<?= base_url('teacher/dashboard') ?>">
                ITE311 FUNDAR LMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="<?= base_url('teacher/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="<?= base_url('teacher/courses') ?>">My Courses</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text text-dark">
                            <?= esc($user['name']) ?>
                            <span class="badge bg-dark ms-2">TEACHER</span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h2 class="mb-0">My Courses</h2>
                        <p class="mb-0">Manage your courses and enrolled students</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="input-group">
                    <input type="text" id="courseSearch" class="form-control" placeholder="Search courses by title...">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                </div>
            </div>
        </div>

        <!-- Flash messages -->
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

        <!-- Courses List -->
        <?php if (empty($courses)): ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <h4>No Courses Assigned</h4>
                    <p class="text-muted">You don't have any courses assigned yet. Please contact the administrator.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card course-card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                <p class="card-text text-muted">
                                    <?= esc($course['description'] ?? 'No description available') ?>
                                </p>
                                
                                <div class="mb-3">
                                    <span class="badge bg-<?= $course['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst(esc($course['status'])) ?>
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Created: <?= date('M d, Y', strtotime($course['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="<?= base_url("teacher/course/{$course['id']}/students") ?>" 
                                       class="btn btn-primary">
                                        Manage Students
                                    </a>
                                    <a href="<?= base_url("teacher/course/{$course['id']}/upload") ?>" 
                                       class="btn btn-success">
                                        Upload Materials
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary Card -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Course Summary</h5>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h3 class="text-primary"><?= count($courses) ?></h3>
                                    <p class="mb-0">Total Courses</p>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-success">
                                        <?= count(array_filter($courses, function($c) { return $c['status'] === 'active'; })) ?>
                                    </h3>
                                    <p class="mb-0">Active Courses</p>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-warning">
                                        <?= count(array_filter($courses, function($c) { return $c['status'] === 'inactive'; })) ?>
                                    </h3>
                                    <p class="mb-0">Inactive Courses</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Search Script -->
    <script>
        document.getElementById('courseSearch').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const courseCards = document.querySelectorAll('.col-md-6.mb-4');
            
            courseCards.forEach(function(card) {
                const text = card.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
