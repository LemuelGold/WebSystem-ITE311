<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard - LMS' ?></title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Admin dashboard page -->
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('admin/dashboard') ?>">LMS - Admin</a>
            <div class="navbar-nav ms-auto">
                <!-- Display logged-in admin's name -->
                <span class="navbar-text text-white">
                    <?= esc($user['name']) ?> (Administrator)
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
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h2>Admin Dashboard - Welcome, <?= esc($user['name']) ?>!</h2>
                        <p>Manage users, courses, and system settings</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary"><?= $stats['totalUsers'] ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success"><?= $stats['studentCount'] ?></h3>
                        <p>Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning"><?= $stats['teacherCount'] ?></h3>
                        <p>Teachers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info"><?= $stats['totalCourses'] ?></h3>
                        <p>Courses</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Actions -->
        <div class="row mb-4">
            <div class="col-md-6">
                <!-- Admin management options -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Management Tools</h5>
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-primary">Manage Users</a>
                            <a href="<?= base_url('admin/courses') ?>" class="btn btn-outline-success">Manage Courses</a>
                            <a href="<?= base_url('admin/reports') ?>" class="btn btn-outline-info">View Reports</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Recent users info -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Users</h5>
                        <?php if (!empty($recentUsers)): ?>
                            <?php foreach (array_slice($recentUsers, 0, 3) as $recentUser): ?>
                                <p><strong><?= esc($recentUser['name']) ?></strong> - <?= ucfirst($recentUser['role']) ?></p>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No recent users.</p>
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