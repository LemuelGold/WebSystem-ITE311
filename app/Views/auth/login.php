<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LMS</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Login page with centered form layout -->
<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <!-- Login form card -->
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2>LMS</h2>
                            <p class="text-muted">Please sign in</p>
                        </div>

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

                        <!-- Login form -->
                        <form method="post" action="<?= base_url('login') ?>">
                            <div class="mb-3">
                                <label for="login" class="form-label">Email or Username</label>
                                <input type="text" class="form-control" id="login" name="login" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                        </form>

                        <!-- Link to register page -->
                        <div class="text-center mt-3">
                            <span>Don't have an account? <a href="<?= base_url('register') ?>" class="btn btn-link p-0">Create Account</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript for interactive components -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>