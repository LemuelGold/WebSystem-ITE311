<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'User Management - Admin Panel' ?></title>
    <!-- Bootstrap for styling - keeping it simple for student project -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Simple styling for the user management interface */
        .user-card {
            transition: transform 0.2s;
        }
        .user-card:hover {
            transform: translateY(-2px);
        }
        .role-badge {
            font-size: 0.8em;
        }
        /* Different colors for different roles */
        .role-admin { background-color: #dc3545 !important; }
        .role-teacher { background-color: #ffc107 !important; color: #000 !important; }
        .role-student { background-color: #0d6efd !important; }
    </style>
</head>
<body class="bg-light">
    <!-- Simple navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url('admin/dashboard') ?>">
                ITE311 FUNDAR LMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-white" href="<?= base_url('admin/users') ?>">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= base_url('admin/courses') ?>">Courses</a>
                    </li>
                </ul>
                
                <!-- Notification Bell Icon -->
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative text-white" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                            </svg>
                            <span id="notificationBadge" class="badge bg-warning position-absolute top-0 start-100 translate-middle rounded-pill" style="display: none; font-size: 0.65rem;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="width: 300px;">
                            <div class="dropdown-header">Notifications</div>
                            <div class="dropdown-divider"></div>
                            <div class="text-center py-3 text-muted">
                                <small>No notifications</small>
                            </div>
                        </div>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= esc($user['name']) ?? 'Admin Panel' ?>
                            <span class="badge bg-light text-dark ms-2">ADMIN</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page header with add user button -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-danger text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">User Management</h2>
                            <p class="mb-0">Manage all system users - add, edit, and view user information</p>
                        </div>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            Add New User
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- User statistics cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-danger"><?= $stats['adminCount'] ?></h3>
                        <p class="mb-0">Administrators</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning"><?= $stats['teacherCount'] ?></h3>
                        <p class="mb-0">Teachers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary"><?= $stats['studentCount'] ?></h3>
                        <p class="mb-0">Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info"><?= $stats['totalUsers'] ?></h3>
                        <p class="mb-0">Total Users</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users table -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">All Users</h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($users)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td>
                                            <strong><?= esc($user['name']) ?></strong>
                                            <?php if ($user['id'] == $currentUserId): ?>
                                                <span class="badge bg-info ms-2">You</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td>
                                            <span class="badge role-<?= $user['role'] ?> role-badge">
                                                <?= strtoupper($user['role']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                        <td>
                                            <?php if ($user['id'] != $currentUserId): ?>
                                                <!-- Can edit other users (including other admins) -->
                                                <button class="btn btn-sm btn-primary" 
                                                        onclick="editUser(<?= $user['id'] ?>, '<?= esc($user['name']) ?>', '<?= esc($user['email']) ?>', '<?= $user['role'] ?>')">
                                                    Edit
                                                </button>
                                                
                                                <?php if ($user['role'] != 'admin'): ?>
                                                    <!-- Can only delete non-admin users -->
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="deleteUser(<?= $user['id'] ?>, '<?= esc($user['name']) ?>')">
                                                        Delete
                                                    </button>
                                                <?php else: ?>
                                                    <!-- Cannot delete admin accounts -->
                                                    <button class="btn btn-sm btn-secondary" disabled 
                                                            title="Admin accounts cannot be deleted">
                                                        <i class="bi bi-shield-lock"></i> Protected
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <!-- Current user - cannot edit or delete self -->
                                                <span class="badge bg-secondary">Your Account (Protected)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <p class="text-muted">No users found in the system.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('admin/users/create') ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="addName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="addName" name="name" required 
                                   pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]+" 
                                   title="Name can only contain letters, spaces, and Spanish characters (ñÑáéíóúÁÉÍÓÚüÜ)"
                                   placeholder="e.g., María José, Juan Ñuñez">
                        </div>
                        <div class="mb-3">
                            <label for="addEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="addEmail" name="email" required
                                   placeholder="e.g., maria.jose@gmail.com">
                        </div>
                        <div class="mb-3">
                            <label for="addPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="addPassword" name="password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="addRole" class="form-label">User Role</label>
                            <select class="form-select" id="addRole" name="role" required>
                                <option value="">Select Role...</option>
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('admin/users/update') ?>" method="POST">
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required
                                   pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ\s]+" 
                                   title="Name can only contain letters, spaces, and Spanish characters (ñÑáéíóúÁÉÍÓÚüÜ)">
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">User Role</label>
                            <select class="form-select" id="editRole" name="role" required>
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="editPassword" name="password" minlength="6">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
                    <p class="text-danger mb-0"><i class="bi bi-exclamation-triangle"></i> This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="<?= base_url('admin/users/delete') ?>" method="POST" style="display: inline;">
                        <input type="hidden" id="deleteUserId" name="user_id">
                        <button type="submit" class="btn btn-danger">Delete User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // JavaScript functions for user management
        function editUser(id, name, email, role) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            
            // Show the edit modal
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }
        
        function deleteUser(id, name) {
            document.getElementById('deleteUserId').value = id;
            document.getElementById('deleteUserName').textContent = name;
            
            // Show the delete confirmation modal
            new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
        }
    </script>
</body>
</html>