<?php
$title = 'User Management - Admin';
include(APPPATH . 'Views/template.php');
?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold mb-0"><i class="fas fa-users-cog me-2 text-danger"></i>User Management</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= site_url('admin/users/add') ?>" class="btn btn-danger btn-sm" style="border: 2px solid #000;">
                <i class="fas fa-plus me-2"></i>Add New User
            </a>
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

    <!-- Users Table -->
    <div class="card shadow-sm" style="border: 2px solid #000 !important;">
        <div class="card-body p-3">
            <?php if (empty($users)): ?>
                <p class="text-muted text-center py-3 mb-0">No users found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead>
                            <tr>
                                <th class="py-2">ID</th>
                                <th class="py-2">Name</th>
                                <th class="py-2">Email</th>
                                <th class="py-2">Role</th>
                                <th class="py-2">Created At</th>
                                <th class="py-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <?php 
                                $isDeleted = !empty($user['deleted_at']);
                                ?>
                                <tr class="<?= $isDeleted ? 'text-muted' : '' ?>" style="<?= $isDeleted ? 'opacity: 0.6;' : '' ?>">
                                    <td class="py-2"><?= esc($user['id']) ?></td>
                                    <td class="py-2">
                                        <?= esc($user['name']) ?>
                                        <?php if ($isDeleted): ?>
                                            <span class="badge bg-secondary ms-2">Deleted</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2"><?= esc($user['email']) ?></td>
                                    <td class="py-2">
                                        <?php
                                        $role = strtolower($user['role'] ?? 'student');
                                        $badgeClass = 'secondary';
                                        if ($role === 'admin') $badgeClass = 'danger';
                                        elseif ($role === 'teacher') $badgeClass = 'primary';
                                        elseif ($role === 'student') $badgeClass = 'success';
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($role) ?></span>
                                    </td>
                                    <td class="py-2"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td class="py-2 text-center">
                                        <a href="<?= site_url('admin/users/edit/' . $user['id']) ?>" 
                                           class="btn btn-sm btn-primary me-1" 
                                           style="border: 2px solid #000; padding: 0.25rem 0.5rem;"
                                           title="<?= $isDeleted ? 'Edit/Restore User' : 'Edit User' ?>">
                                            <i class="fas fa-<?= $isDeleted ? 'undo' : 'edit' ?>"></i>
                                        </a>
                                        <?php if (!$isDeleted && $user['id'] != session()->get('userID')): ?>
                                            <a href="<?= site_url('admin/users/delete/' . $user['id']) ?>" 
                                               class="btn btn-sm btn-danger" 
                                               style="border: 2px solid #000; padding: 0.25rem 0.5rem;"
                                               title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php elseif ($isDeleted): ?>
                                            <span class="text-muted" title="User is deleted">
                                                <i class="fas fa-ban"></i>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted" title="Cannot delete your own account">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

