<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'User Management - Admin Panel' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Force browser refresh -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
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
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #333 !important;
        }
        
        .user-card {
            transition: transform 0.2s;
        }
        .user-card:hover {
            transform: translateY(-2px);
        }
        .role-badge {
            font-size: 0.8em;
        }
        .role-admin { background-color: #dc3545 !important; }
        .role-teacher { background-color: #ffc107 !important; color: #000 !important; }
        .role-student { background-color: #0d6efd !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <span class="navbar-brand" style="cursor: default;">LMS</span>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('admin/users') ?>">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/courses') ?>">Courses</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('logout') ?>">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
                        </div>
                    </li>
                </ul>

            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page header with add user button -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card" style="border: 2px solid #000;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0 text-dark">User Management</h2>
                        </div>
                        <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#addUserModal">
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
                                            <!-- All users can be edited -->
                                            <button class="btn btn-sm btn-primary" 
                                                    onclick="editUser(<?= $user['id'] ?>, '<?= esc($user['name']) ?>', '<?= esc($user['email']) ?>', '<?= $user['role'] ?>')"
                                                    title="Edit User">
                                                <i class="bi bi-pencil-square" style="font-size: 14px;"></i>
                                            </button>
                                            
                                            <?php if ($user['role'] == 'admin'): ?>
                                                <!-- Admin users cannot be deleted (role protected) -->
                                                <button class="btn btn-sm btn-secondary" disabled 
                                                        title="Admin accounts cannot be deleted">
                                                    🔒
                                                </button>
                                            <?php elseif ($user['id'] != $currentUserId): ?>
                                                <!-- Can delete non-admin users -->
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="deleteUser(<?= $user['id'] ?>, '<?= esc($user['name']) ?>')"
                                                        title="Delete User">
                                                    <i class="bi bi-trash" style="font-size: 14px;"></i>
                                                </button>
                                            <?php else: ?>
                                                <!-- Cannot delete yourself -->
                                                <button class="btn btn-sm btn-secondary" disabled 
                                                        title="Cannot delete your own account">
                                                    🔒
                                                </button>
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
            <div class="modal-content" style="border: 2px solid #000; background-color: white;">
                <div class="modal-header" style="background-color: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title text-dark"><i class="bi bi-trash"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background-color: white; padding: 1.5rem;">
                    <p class="text-dark mb-3">Are you sure you want to delete user <strong id="deleteUserName" class="text-danger"></strong>?</p>
                    <div class="alert alert-warning" style="border: 1px solid #ffc107; background-color: #fff3cd;">
                        <i class="bi bi-exclamation-triangle text-warning"></i> This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer" style="background-color: white; border-top: 1px solid #ddd;">
                    <button type="button" class="btn" style="background-color: white; border: 2px solid #6c757d; color: #6c757d;" data-bs-dismiss="modal">Cancel</button>
                    <form action="<?= base_url('admin/users/delete') ?>" method="POST" style="display: inline;">
                        <input type="hidden" id="deleteUserId" name="user_id">
                        <button type="submit" class="btn" style="background-color: white; border: 2px solid #dc3545; color: #dc3545;" onmouseover="this.style.backgroundColor='#dc3545'; this.style.color='white';" onmouseout="this.style.backgroundColor='white'; this.style.color='#dc3545';">Delete User</button>
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
            
            // If admin user, disable the role field
            const roleSelect = document.getElementById('editRole');
            if (role === 'admin') {
                roleSelect.disabled = true;
                roleSelect.title = 'Admin role cannot be changed';
            } else {
                roleSelect.disabled = false;
                roleSelect.title = '';
            }
            
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
    
    <!-- jQuery and Notification Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
</body>
</html>