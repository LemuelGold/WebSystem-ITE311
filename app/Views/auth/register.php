<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome --> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>"><i class="fas fa-graduation-cap me-2"></i>LMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: #000;">
      <span class="navbar-toggler-icon" style="background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%280, 0, 0, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e\");"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/about') ?>">About</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/contact') ?>">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/login') ?>">Login</a></li>
        <li class="nav-item"><a class="nav-link active" href="<?= base_url('/register') ?>">Register</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 76px); padding: 20px 0;">

    <div class="card shadow p-4" style="max-width: 420px; width: 100%; border: 2px solid #000 !important;">

        <h3 class="text-center fw-bold mb-1 text-dark">LMS</h3>
        <p class="text-center mb-4 text-muted">Create your account</p>

        <!-- Success Message -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success py-2">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('register') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label text-dark">Full Name</label>
                <input 
                    type="text" 
                    name="name" 
                    class="form-control" style="border: 2px solid #000 !important;" 
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label text-dark">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-control" style="border: 2px solid #000 !important;" 
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label text-dark">Username</label>
                <input 
                    type="text" 
                    name="username" 
                    class="form-control" style="border: 2px solid #000 !important;" 
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label text-dark">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    class="form-control" style="border: 2px solid #000 !important;" 
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label text-dark">Confirm Password</label>
                <input 
                    type="password" 
                    name="confirm_password" 
                    class="form-control" style="border: 2px solid #000 !important;" 
                    required
                >
            </div>

            <button type="submit" class="btn btn-dark w-100 fw-semibold mt-2" style="border: 2px solid #000;">
                Create Account
            </button>
        </form>

        <div class="text-center mt-3">
            <span class="text-muted">Already have an account?</span>
            <a href="<?= base_url('login') ?>" class="fw-semibold text-dark text-decoration-none">
                Sign In
            </a>
        </div>

    </div>

</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

