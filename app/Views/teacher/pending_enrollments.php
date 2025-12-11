<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pending Enrollment Requests' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .navbar { background-color: #ffc107 !important; }
        .navbar .nav-link, .navbar .navbar-brand { color: #000 !important; }
        .pending-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .pending-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('teacher/dashboard') ?>">ITE311 FUNDAR LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('teacher/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('teacher/courses') ?>">My Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('teacher/pending-enrollments') ?>">Pending Requests</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= esc($user['name']) ?> <span class="badge bg-warning text-dark">TEACHER</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a></li>
                        </ul>
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

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h2 class="mb-0">
                            <i class="bi bi-person-plus"></i> Pending Enrollment Requests
                            <span class="badge bg-danger"><?= count($pendingEnrollments) ?></span>
                        </h2>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pendingEnrollments)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                                <h4 class="mt-3 text-muted">No Pending Enrollment Requests</h4>
                                <p class="text-muted">All enrollment requests have been processed.</p>
                                <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-primary mt-3">
                                    Back to Dashboard
                                </a>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-4">
                                Review and approve or reject enrollment requests from students for your courses.
                            </p>

                            <div class="row">
                                <?php foreach ($pendingEnrollments as $enrollment): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card pending-card border-warning">
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
                                                    <span class="badge bg-warning text-dark">Pending</span>
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
                                                        Requested: <?= date('M d, Y h:i A', strtotime($enrollment['created_at'])) ?>
                                                    </small>
                                                </div>

                                                <div class="d-flex gap-2">
                                                    <form method="POST" action="<?= base_url('teacher/enrollment/approve') ?>" class="flex-fill" onsubmit="return confirm('Approve this enrollment request?');">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="enrollment_id" value="<?= $enrollment['id'] ?>">
                                                        <button type="submit" class="btn btn-success w-100">
                                                            <i class="bi bi-check-circle"></i> Approve
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="<?= base_url('teacher/enrollment/reject') ?>" class="flex-fill" onsubmit="return confirm('Reject this enrollment request?');">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="enrollment_id" value="<?= $enrollment['id'] ?>">
                                                        <button type="submit" class="btn btn-danger w-100">
                                                            <i class="bi bi-x-circle"></i> Reject
                                                        </button>
                                                    </form>
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
