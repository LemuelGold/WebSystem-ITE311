<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Course Materials - LMS' ?></title>
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
        
        .material-card {
            transition: transform 0.2s;
            border: 1px solid #ddd;
        }
        
        .material-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 6px;
            padding: 1.2rem;
            text-align: center;
            background-color: #f8f9fa;
            transition: border-color 0.3s ease;
        }
        
        .upload-area:hover {
            border-color: #007bff;
        }
        
        .file-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <span class="navbar-brand" style="cursor: default;">🎓 LMS</span>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url($user['role'] . '/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url($user['role'] . '/courses') ?>">
                            <?= $user['role'] === 'admin' ? 'Courses' : 'My Courses' ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Materials</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <?= esc($user['name']) ?> (<?= strtoupper($user['role']) ?>)
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card" style="border: 2px solid #000;">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0 text-dark"><?= esc($course['title']) ?></h5>
                                <small class="text-muted">Course Materials Management</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">Course ID: <?= esc($course['id']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
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

        <!-- Upload Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card material-card">
                    <div class="card-body py-3">
                        <h6 class="card-title mb-2">📁 Upload New Material</h6>
                        
                        <?php
                        // Make form action role-aware - teachers post to teacher route, admins to admin route
                        $uploadAction = base_url("{$user['role']}/course/{$course['id']}/upload");
                        ?>
                        <form action="<?= $uploadAction ?>" method="POST" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">📚 Academic Period *</label>
                                    <select class="form-select" name="period" required>
                                        <option value="">-- Select Period --</option>
                                        <option value="Prelim">📖 Prelim</option>
                                        <option value="Midterm">📚 Midterm</option>
                                        <option value="Final">🎓 Final</option>
                                    </select>
                                    <small class="text-muted">Choose the academic period for this material</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">📄 Material Title (Optional)</label>
                                    <input type="text" class="form-control" name="material_title" placeholder="e.g., Chapter 1 - Introduction" maxlength="100">
                                    <small class="text-muted">Custom title for the material (optional)</small>
                                </div>
                            </div>
                            <div class="upload-area mb-3">
                                <div class="file-icon">📄</div>
                                <small class="fw-bold">Choose File to Upload</small>
                                <input type="file" class="form-control mt-2" id="material_file" name="material_file" required accept=".pdf,.doc,.docx">
                                <small class="text-muted mt-1 d-block">
                                    📄 PDF, 📝 DOC, 📝 DOCX only (Max: 10MB)
                                </small>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-outline-dark btn-sm px-3">
                                    📤 Upload Material
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Materials List -->
        <div class="row">
            <div class="col-12">
                <div class="card material-card">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title mb-0">📚 Course Materials</h6>
                            <span class="badge bg-secondary"><?= count($materials) ?> files</span>
                        </div>
                        
                        <?php if (empty($materials)): ?>
                            <div class="text-center py-3">
                                <div class="mb-2" style="font-size: 2rem;">📭</div>
                                <small class="text-muted d-block">No materials uploaded yet</small>
                                <small class="text-muted">Upload your first course material using the form above.</small>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Material</th>
                                            <th>Period</th>
                                            <th>Upload Date</th>
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <th>Uploaded By</th>
                                            <?php endif; ?>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($materials as $material): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">
                                                            <?php
                                                            $extension = strtolower(pathinfo($material['file_name'], PATHINFO_EXTENSION));
                                                            switch($extension) {
                                                                case 'pdf': echo '📄'; break;
                                                                case 'doc':
                                                                case 'docx': echo '📝'; break;
                                                                default: echo '📋'; break;
                                                            }
                                                            ?>
                                                        </span>
                                                        <div>
                                                            <strong><?= esc($material['material_title'] ?? $material['file_name']) ?></strong>
                                                            <?php if (!empty($material['material_title']) && $material['material_title'] !== $material['file_name']): ?>
                                                                <br><small class="text-muted"><?= esc($material['file_name']) ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (!empty($material['period'])): ?>
                                                        <?php
                                                        $periodIcon = '';
                                                        $periodClass = '';
                                                        switch($material['period']) {
                                                            case 'Prelim':
                                                                $periodIcon = '📖';
                                                                $periodClass = 'bg-info';
                                                                break;
                                                            case 'Midterm':
                                                                $periodIcon = '📚';
                                                                $periodClass = 'bg-warning text-dark';
                                                                break;
                                                            case 'Final':
                                                                $periodIcon = '🎓';
                                                                $periodClass = 'bg-success';
                                                                break;
                                                        }
                                                        ?>
                                                        <span class="badge <?= $periodClass ?>">
                                                            <?= $periodIcon ?> <?= esc($material['period']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">📋 General</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?= date('M d, Y', strtotime($material['created_at'])) ?></small><br>
                                                    <small class="text-muted"><?= date('H:i', strtotime($material['created_at'])) ?></small>
                                                </td>
                                                <?php if ($user['role'] === 'admin'): ?>
                                                    <td>
                                                        <?php if (isset($material['uploaded_by'])): ?>
                                                            <small>User #<?= $material['uploaded_by'] ?></small>
                                                        <?php else: ?>
                                                            <small class="text-muted">Unknown</small>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endif; ?>
                                                <td>
                                                    <?php if (isset($material['status'])): ?>
                                                        <?php if ($material['status'] === 'approved'): ?>
                                                            <span class="badge bg-success">✅ Approved</span>
                                                        <?php elseif ($material['status'] === 'pending'): ?>
                                                            <span class="badge bg-warning text-dark">⏳ Pending</span>
                                                        <?php elseif ($material['status'] === 'rejected'): ?>
                                                            <span class="badge bg-danger">❌ Rejected</span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">✅ Available</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="<?= base_url("materials/download/{$material['id']}") ?>" 
                                                           class="btn btn-sm btn-outline-primary" title="Download">
                                                            💾
                                                        </a>
                                                        <?php if ($user['role'] === 'admin' || $user['role'] === 'teacher'): ?>
                                                            <button onclick="deleteMaterial(<?= $material['id'] ?>, '<?= esc($material['file_name']) ?>')" 
                                                                    class="btn btn-sm btn-outline-danger" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
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
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border: 2px solid #000;">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark"><i class="bi bi-trash"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div style="font-size: 3rem;">⚠️</div>
                    </div>
                    <p class="text-center">Are you sure you want to delete this material?</p>
                    <div class="alert alert-warning text-center">
                        <strong id="delete_file_name"></strong>
                    </div>
                    <p class="text-muted text-center mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="delete_confirm_link" class="btn btn-outline-danger">Delete Material</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteMaterial(materialId, fileName) {
            document.getElementById('delete_file_name').textContent = fileName;
            document.getElementById('delete_confirm_link').href = '<?= base_url("materials/delete/") ?>' + materialId;
            
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>
