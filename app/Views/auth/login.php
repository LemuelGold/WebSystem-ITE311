<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: white;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        .main-container {
            background-color: white;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        .navbar {
            background-color: white;
            box-shadow: none;
            padding: 0.5rem 0;
            border-bottom: 2px solid #ddd;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: #333 !important;
            font-size: 1.5rem;
        }
        
        .navbar-brand::before {
            content: "🎓";
            margin-right: 8px;
        }
        
        .navbar-nav .nav-link {
            color: #666 !important;
            font-weight: 500;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: #333 !important;
        }
        
        .login-container {
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 80px);
            padding: 2rem;
        }
        
        .login-card {
            background-color: white;
            border: 2px solid #000;
            border-radius: 15px;
            padding: 3rem 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .login-title {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        
        .form-label {
            color: #333;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.75rem;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .form-control:focus {
            border-color: #333;
            box-shadow: 0 0 0 0.2rem rgba(51, 51, 51, 0.25);
        }
        
        .btn-signin {
            background-color: #333;
            border: none;
            color: white;
            padding: 0.75rem;
            border-radius: 4px;
            font-weight: 500;
            width: 100%;
            margin-bottom: 1rem;
        }
        
        .btn-signin:hover {
            background-color: #555;
        }
        
        .register-link {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .register-link a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .back-home {
            text-align: center;
        }
        
        .back-home a {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .back-home a:hover {
            color: #333;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="<?= base_url('/') ?>">LMS</a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/about') ?>">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/contact') ?>">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('/login') ?>">Login</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <div class="login-container">
            <div class="login-card">
                <h1 class="login-title">LMS</h1>
                <p class="login-subtitle">Welcome back — sign in</p>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('login') ?>">
                    <div class="mb-3">
                        <label for="login" class="form-label">Email or Username</label>
                        <input type="text" class="form-control" id="login" name="login" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-signin">Sign In</button>
                </form>

                <div class="register-link">
                    Don't have an account? <a href="<?= base_url('register') ?>">Register</a>
                </div>
                
                <div class="back-home">
                    <a href="<?= base_url('/') ?>">Go back to homepage</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>