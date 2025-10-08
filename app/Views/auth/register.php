<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LMS</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<!-- Registration page with form layout -->
<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Registration form card -->
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h2>Join LMS</h2>
                            <p class="text-muted">Create your account</p>
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

                        <?php if (session()->getFlashdata('errors')): ?>
                            <!-- Validation errors display -->
                            <div class="alert alert-danger">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Registration form -->
                        <form method="post" action="<?= base_url('register') ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= old('name') ?>" required
                                       pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]+" 
                                       title="Name can only contain letters, spaces, and Spanish characters (ñÑáéíóúÁÉÍÓÚüÜ)"
                                       placeholder="e.g., María José, Juan Ñuñez">
                                <div class="form-text">Only letters and Spanish characters allowed</div>
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['name'])): ?>
                                    <div class="text-danger mt-1"><?= session()->getFlashdata('errors')['name'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email') ?>" required
                                       placeholder="e.g., maria.jose@gmail.com">
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email'])): ?>
                                    <div class="text-danger mt-1"><?= session()->getFlashdata('errors')['email'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Minimum 6 characters</div>
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password'])): ?>
                                    <div class="text-danger mt-1"><?= session()->getFlashdata('errors')['password'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password_confirm'])): ?>
                                    <div class="text-danger mt-1"><?= session()->getFlashdata('errors')['password_confirm'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Create Account</button>
                        </form>

                        <!-- Link to login page -->
                        <div class="text-center mt-3">
                            <span>Already have an account? <a href="<?= base_url('login') ?>" class="btn btn-link p-0">Sign In</a></span>
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