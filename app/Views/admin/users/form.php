<?php
helper('form');
$isEdit = isset($user) && !empty($user);
$title = $isEdit ? 'Edit User - Admin' : 'Add New User - Admin';
include(APPPATH . 'Views/template.php');
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold">
                <i class="fas fa-<?= $isEdit ? 'edit' : 'user-plus' ?> me-2"></i>
                <?= $isEdit ? 'Edit User' : 'Add New User' ?>
            </h2>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="card shadow-sm" style="border: 2px solid #000 !important;">
        <div class="card-body">
            <?php 
            $isDeleted = isset($user) && !empty($user) && !empty($user['deleted_at']);
            if ($isDeleted): 
            ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>This user is currently deleted.</strong> Updating this user will restore them automatically.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php 
            $validation = $validation ?? session()->getFlashdata('validation');
            if ($validation): 
            ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= $isEdit ? site_url('admin/users/update/' . ($user['id'] ?? '')) : site_url('admin/users/store') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               value="<?= esc(old('name', $user['name'] ?? '')) ?>" 
                               required
                               pattern="[A-Za-z0-9\s]+"
                               title="Name can only contain letters, numbers, and spaces. Special characters are not allowed."
                               style="border: 2px solid #000;">
                        <?php if ($validation && $validation->hasError('name')): ?>
                            <div class="text-danger small"><?= $validation->getError('name') ?></div>
                        <?php endif; ?>
                        <small class="text-muted">Only letters, numbers, and spaces are allowed</small>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?= esc(old('email', $user['email'] ?? '')) ?>" 
                               required
                               style="border: 2px solid #000;">
                        <?php if ($validation && $validation->hasError('email')): ?>
                            <div class="text-danger small"><?= $validation->getError('email') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-bold">
                            Password <?= $isEdit ? '' : '<span class="text-danger">*</span>' ?>
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               <?= $isEdit ? '' : 'required' ?>
                               minlength="6"
                               style="border: 2px solid #000;"
                               placeholder="<?= $isEdit ? 'Leave blank to keep current password' : '' ?>">
                        <?php if ($validation && $validation->hasError('password')): ?>
                            <div class="text-danger small"><?= $validation->getError('password') ?></div>
                        <?php endif; ?>
                        <?php if ($isEdit): ?>
                            <small class="text-muted">Leave blank to keep the current password</small>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                        <?php 
                        $selectedRole = old('role', $user['role'] ?? '');
                        $isAdminUser = ($isEdit && strtolower($selectedRole) === 'admin');
                        ?>
                        <select class="form-select" 
                                id="role" 
                                name="role" 
                                required
                                <?= $isAdminUser ? 'disabled' : '' ?>
                                style="border: 2px solid #000; <?= $isAdminUser ? 'background-color: #e9ecef; cursor: not-allowed;' : '' ?>">
                            <option value="">Select Role</option>
                            <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="teacher" <?= $selectedRole === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                            <option value="student" <?= $selectedRole === 'student' ? 'selected' : '' ?>>Student</option>
                        </select>
                        <?php if ($isAdminUser): ?>
                            <input type="hidden" name="role" value="admin">
                            <small class="text-muted"><i class="fas fa-lock me-1"></i>Admin role cannot be changed</small>
                        <?php endif; ?>
                        <?php if ($validation && $validation->hasError('role')): ?>
                            <div class="text-danger small"><?= $validation->getError('role') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-danger me-2" style="border: 2px solid #000;">
                            <i class="fas fa-save me-2"></i><?= $isEdit ? 'Update User' : 'Add User' ?>
                        </button>
                        <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary" style="border: 2px solid #000;">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

