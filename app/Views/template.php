<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RESTAURO</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #FFD700;
      --secondary-color: #FFA500;
      --accent-color: #FF6B6B;
      --success-color: #32CD32;
      --warning-color: #FFD700;
      --info-color: #87CEEB;
      --dark-color: #1a1a1a;
      --light-color: #f8f8f8;
      --gradient-primary: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
      --gradient-secondary: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
      --gradient-gold: linear-gradient(135deg, #FFD700 0%, #FFA500 50%, #FFD700 100%);
      --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.3);
      --shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.4);
      --shadow-gold: 0 4px 20px rgba(255, 215, 0, 0.3);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--gradient-primary);
      color: #ffffff;
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
    }

    /* Animated Background */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: 
        radial-gradient(circle at 20% 80%, rgba(255, 215, 0, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 165, 0, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255, 215, 0, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 60% 60%, rgba(0, 0, 0, 0.8) 0%, transparent 70%);
      animation: backgroundShift 20s ease-in-out infinite;
      z-index: 0;
    }
    
    @keyframes backgroundShift {
      0%, 100% { transform: translateX(0) translateY(0) rotate(0deg); }
      33% { transform: translateX(30px) translateY(-30px) rotate(1deg); }
      66% { transform: translateX(-20px) translateY(20px) rotate(-1deg); }
    }

    .navbar {
      background: rgba(0, 0, 0, 0.95);
      backdrop-filter: blur(20px);
      border-bottom: 2px solid var(--primary-color);
      box-shadow: var(--shadow-soft);
      position: relative;
      z-index: 1000;
    }

    .navbar-brand {
      color: var(--primary-color) !important;
      font-weight: 800;
      font-size: 28px;
      text-decoration: none;
      letter-spacing: -0.5px;
      transition: all 0.3s ease;
      text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
    }

    .navbar-brand:hover {
      color: var(--secondary-color) !important;
      transform: scale(1.05);
      text-shadow: 0 0 15px rgba(255, 165, 0, 0.5);
    }

    .navbar-nav .nav-link {
      color: var(--primary-color) !important;
      font-weight: 600;
      padding: 0.5rem 1rem;
      transition: all 0.3s ease;
      border-radius: 5px;
      margin: 0 0.25rem;
    }

    .navbar-nav .nav-link:hover {
      color: var(--secondary-color) !important;
      background-color: rgba(255, 215, 0, 0.1);
      transform: translateY(-2px);
      text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
    }

    .navbar-toggler {
      border-color: var(--primary-color);
      background: rgba(255, 215, 0, 0.1);
    }

    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 215, 0, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Main content styling */
    .main-content {
      position: relative;
      z-index: 1;
      min-height: calc(100vh - 80px);
      padding: 2rem 0;
    }

    /* Card styling */
    .card {
      background: rgba(0, 0, 0, 0.8);
      border: 2px solid var(--primary-color);
      border-radius: 20px;
      box-shadow: var(--shadow-soft), var(--shadow-gold);
      color: #ffffff;
    }

    .card-header {
      background: var(--gradient-gold);
      color: #000;
      border-bottom: 2px solid var(--primary-color);
      font-weight: 700;
    }

    .card-title {
      color: var(--primary-color);
      text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
    }

    /* Button styling */
    .btn-primary {
      background: var(--gradient-gold);
      border: 2px solid var(--primary-color);
      color: #000;
      font-weight: 600;
      box-shadow: var(--shadow-gold);
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background: var(--gradient-secondary);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
      color: #000;
    }

    .btn-outline-primary {
      border: 2px solid var(--primary-color);
      color: var(--primary-color);
      background: transparent;
    }

    .btn-outline-primary:hover {
      background: var(--gradient-gold);
      color: #000;
      border-color: var(--primary-color);
    }

    /* Form styling */
    .form-control {
      background: rgba(0, 0, 0, 0.5);
      border: 2px solid var(--primary-color);
      color: #ffffff;
      border-radius: 10px;
    }

    .form-control:focus {
      background: rgba(0, 0, 0, 0.7);
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.2);
      color: #ffffff;
    }

    .form-control::placeholder {
      color: rgba(255, 215, 0, 0.6);
    }

    .form-label {
      color: var(--primary-color);
      font-weight: 600;
    }

    /* Alert styling */
    .alert {
      border-radius: 15px;
      border: none;
      box-shadow: var(--shadow-soft);
    }

    .alert-success {
      background: linear-gradient(135deg, #d4edda, #c3e6cb);
      color: #155724;
      border-left: 4px solid var(--success-color);
    }

    .alert-danger {
      background: linear-gradient(135deg, #f8d7da, #f5c6cb);
      color: #721c24;
      border-left: 4px solid #dc3545;
    }

    .alert-info {
      background: linear-gradient(135deg, #d1ecf1, #bee5eb);
      color: #0c5460;
      border-left: 4px solid var(--info-color);
    }

    /* Text styling */
    h1, h2, h3, h4, h5, h6 {
      color: var(--primary-color);
      text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
    }

    .text-muted {
      color: rgba(255, 215, 0, 0.7) !important;
    }

    /* Link styling */
    a {
      color: var(--primary-color);
      text-decoration: none;
      transition: all 0.3s ease;
    }

    a:hover {
      color: var(--secondary-color);
      text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
    }

    /* Table styling */
    .table {
      color: #ffffff;
    }

    .table-dark {
      background: rgba(0, 0, 0, 0.8);
      border: 2px solid var(--primary-color);
    }

    .table-dark th {
      background: var(--gradient-gold);
      color: #000;
      border-color: var(--primary-color);
    }

    .table-dark td {
      border-color: rgba(255, 215, 0, 0.3);
    }

    /* Responsive design */
    @media (max-width: 768px) {
      .navbar-brand {
        font-size: 24px;
      }
      
      .main-content {
        padding: 1rem 0;
      }
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="<?= base_url('/') ?>">
        lms_RESTAURO
      </a>
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('/') ?>">
              <i class="fas fa-home me-1"></i>Home
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('/about') ?>">
              <i class="fas fa-info-circle me-1"></i>About
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('/contact') ?>">
              <i class="fas fa-envelope me-1"></i>Contact
            </a>
          </li>
          <?php if (session()->get('isLoggedIn')): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('/dashboard') ?>">
                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('/logout') ?>">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('/login') ?>">
                <i class="fas fa-sign-in-alt me-1"></i>Login
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('/register') ?>">
                <i class="fas fa-user-plus me-1"></i>Register
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content Wrapper -->
  <div class="main-content">
    <!-- Content will be inserted here by individual pages -->
  </div>

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
