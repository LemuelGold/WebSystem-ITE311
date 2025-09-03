<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard - LMS' ?></title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Dashboard page for logged-in users -->
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('dashboard') ?>">LMS</a>
            <div class="navbar-nav ms-auto">
                <!-- Display logged-in user's name -->
                <span class="navbar-text text-white">
                    <?= esc($user['name']) ?>
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
                        <h2>Welcome, <?= esc($user['name']) ?>!</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="row mb-4">
            <div class="col-md-6">1
                <!-- User information display card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">User Information</h5>
                        <p><strong>Name:</strong> <?= esc($user['name']) ?></p>
                        <p><strong>Email:</strong> <?= esc($user['email']) ?></p>
                        <p><strong>Role:</strong> <?= ucfirst(esc($user['role'])) ?></p>
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