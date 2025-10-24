<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Upload Materials' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg <?= $user['role'] === 'admin' ? 'navbar-dark bg-danger' : 'navbar-light bg-warning' ?>">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="<?= base_url($user['role'] . '/dashboard') ?>">
                ITE311 FUNDAR LMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="<?= base_url($user['role'] . '/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="<?= base_url($user['role'] . '/courses') ?>">
                            <?= $user['role'] === 'admin' ? 'Courses' : 'My Courses' ?>
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text text-dark">
                            <?= esc($user['name']) ?>
                            <span class="badge <?= $user['role'] === 'admin' ? 'bg-light text-dark' : 'bg-dark' ?> ms-2"><?= strtoupper($user['role']) ?></span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h2 class="mb-0"><?= esc($course['title']) ?></h2>
                        <p class="mb-0">Upload and manage course materials</p>
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

        <div class="row">
            <!-- Upload Form -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Upload New Material</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url("admin/course/{$course['id']}/upload") ?>" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="material_file" class="form-label">Select File *</label>
                                <input type="file" class="form-control" id="material_file" name="material_file" required accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.rar">
                                <small class="text-muted">
                                    Allowed: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP, RAR (Max: 10MB)
                                </small>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    Upload Material
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Materials -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Uploaded Materials (<?= count($materials) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($materials)): ?>
                            <div class="alert alert-info mb-0">
                                No materials uploaded yet for this course.
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($materials as $material): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <h6 class="mb-0 me-2"><?= esc($material['file_name']) ?></h6>
                                                    <?php if (isset($material['status'])): ?>
                                                        <?php if ($material['status'] === 'approved'): ?>
                                                            <span class="badge bg-success">Approved</span>
                                                        <?php elseif ($material['status'] === 'pending'): ?>
                                                            <span class="badge bg-warning text-dark">Pending Approval</span>
                                                        <?php elseif ($material['status'] === 'rejected'): ?>
                                                            <span class="badge bg-danger">Rejected</span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted">
                                                    Uploaded: <?= date('M d, Y H:i', strtotime($material['created_at'])) ?>
                                                    <?php if (isset($material['uploaded_by']) && $user['role'] === 'admin'): ?>
                                                        <br>By: User #<?= $material['uploaded_by'] ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url("materials/download/{$material['id']}") ?>" class="btn btn-sm btn-outline-primary">
                                                    Download
                                                </a>
                                                <?php if ($user['role'] === 'admin' || $user['role'] === 'teacher'): ?>
                                                    <button onclick="deleteMaterial(<?= $material['id'] ?>, '<?= esc($material['file_name']) ?>')" class="btn btn-sm btn-outline-danger">
                                                        Delete
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
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
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this material?</p>
                    <p class="text-danger"><strong id="delete_file_name"></strong></p>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="delete_confirm_link" class="btn btn-danger">Delete Material</a>
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
