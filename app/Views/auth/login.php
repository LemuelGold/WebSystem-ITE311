<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LMS</title>
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
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/about') ?>">About</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/contact') ?>">Contact</a></li>
        <li class="nav-item"><a class="nav-link active" href="<?= base_url('/login') ?>">Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 76px); padding: 20px 0;">

    <div class="card shadow p-4" style="max-width: 380px; width: 100%; border: 2px solid #000 !important;">

        <h3 class="text-center fw-bold mb-1 text-dark">LMS</h3>
        <p class="text-center mb-4 text-muted">Welcome back — sign in</p>

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

        <form method="post" action="<?= base_url('login') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label text-dark">Email or Username</label>
                <input 
                    type="text" 
                    name="login" 
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

            <button type="submit" class="btn btn-dark w-100 fw-semibold mt-2" style="border: 2px solid #000;">
                Sign In
            </button>
        </form>

        <div class="text-center mt-3">
            <span class="text-muted">Don't have an account?</span>
            <a href="<?= base_url('register') ?>" class="fw-semibold text-dark text-decoration-none">
                Register
            </a>
        </div>

    </div>

</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
