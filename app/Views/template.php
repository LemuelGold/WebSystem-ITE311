<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'LMS Dashboard' ?></title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light text-dark">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
  <div class="container">
    <div class="d-flex align-items-center">
      <a class="navbar-brand fw-bold me-3" href="<?= base_url('/') ?>"><i class="fas fa-graduation-cap me-2"></i>LMS</a>
      <?php if (session()->get('isLoggedIn')): ?>
        <?php 
        $current_uri = uri_string();
        $is_dashboard = ($current_uri == 'dashboard');
        ?>
        <a class="nav-link<?= $is_dashboard ? ' active fw-semibold' : '' ?>" href="<?= base_url('/dashboard') ?>" style="color: inherit; text-decoration: none;">Dashboard</a>
      <?php endif; ?>
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: #000;">
      <span class="navbar-toggler-icon" style="background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%280, 0, 0, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e\");"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (session()->get('isLoggedIn')): ?>
          <li class="nav-item"><a class="nav-link text-danger" href="<?= base_url('/logout') ?>" onclick="return confirm('Are you sure you want to logout?')">Logout</a></li>
        <?php else: ?>
          <?php 
          $current_uri = uri_string();
          $is_home = ($current_uri == '' || $current_uri == 'home');
          $is_about = ($current_uri == 'about');
          $is_contact = ($current_uri == 'contact');
          $is_login = ($current_uri == 'login');
          $is_register = ($current_uri == 'register');
          ?>
          <li class="nav-item"><a class="nav-link<?= $is_home ? ' active fw-bold' : '' ?>" href="<?= base_url('/') ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link<?= $is_about ? ' active fw-bold' : '' ?>" href="<?= base_url('/about') ?>">About</a></li>
          <li class="nav-item"><a class="nav-link<?= $is_contact ? ' active fw-bold' : '' ?>" href="<?= base_url('/contact') ?>">Contact</a></li>
          <li class="nav-item"><a class="nav-link<?= $is_login ? ' active fw-bold' : '' ?>" href="<?= base_url('/login') ?>">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container py-5">

  <!-- Flash Messages -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <?php if (isset($content)): ?>
    <!-- Custom Content -->
    <?= $content ?>
  <?php else: ?>
    <!-- Default Dashboard Content -->
    <?php 
    $userRole = strtolower(session()->get('role') ?? 'user');
    $isAdmin = ($userRole === 'admin');
    $isTeacher = ($userRole === 'teacher');
    $isStudent = ($userRole === 'student');
    
    // Set role-specific colors and icons
    if ($isAdmin) {
        $roleColor = 'danger';
        $roleIcon = 'user-shield';
        $welcomeText = 'Manage the entire learning management system';
    } elseif ($isTeacher) {
        $roleColor = 'primary';
        $roleIcon = 'chalkboard-teacher';
        $welcomeText = 'Manage your teaching activities and students';
    } else {
        $roleColor = 'success';
        $roleIcon = 'graduation-cap';
        $welcomeText = 'Manage your learning activities efficiently';
    }
    ?>
    
    <!-- Welcome Card -->
    <div class="card mb-4 shadow-sm" style="border: 2px solid #000 !important;">
      <div class="card-body text-center py-4">
        <h2 class="fw-bold mb-3">
          <i class="fas fa-<?= $roleIcon ?> me-2 text-<?= $roleColor ?>"></i>
          Welcome, <?= session()->get('name') ?? 'User' ?>!
        </h2>
        <p class="text-muted mb-0">
          <?= $welcomeText ?>
        </p>
        <p class="text-muted small mt-2">
          <span class="badge bg-<?= $roleColor ?>">
            <?= ucfirst(session()->get('role') ?? 'User') ?>
          </span>
        </p>
      </div>
    </div>

    <!-- Role-specific Features -->
    <?php if (isset($role_content)): ?>
      <?= $role_content ?>
    <?php endif; ?>

    <!-- User Info -->
    <div class="card mb-4 shadow-sm" style="border: 2px solid #000 !important;">
      <div class="card-body">
        <h5 class="fw-bold mb-3"><i class="fas fa-user-circle me-2"></i>Account Information</h5>
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between bg-transparent border-bottom">
            <span class="fw-semibold text-dark">Full Name</span>
            <span class="text-dark"><?= session()->get('name') ?? 'Guest' ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between bg-transparent border-bottom">
            <span class="fw-semibold text-dark">Email</span>
            <span class="text-dark"><?= session()->get('email') ?? 'N/A' ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between bg-transparent">
            <span class="fw-semibold text-dark">Role</span>
            <span class="badge bg-<?= $roleColor ?>">
              <?= ucfirst(session()->get('role') ?? 'User') ?>
            </span>
          </li>
        </ul>
      </div>
    </div>
  <?php endif; ?>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
