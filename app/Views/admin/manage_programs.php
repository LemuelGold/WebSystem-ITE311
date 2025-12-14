<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            background: white;
            border: 2px solid #000;
            border-radius: 0;
            margin: 2rem auto;
            max-width: 1400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header-section {
            background: white;
            border-bottom: 2px solid #000;
            padding: 1.5rem;
        }
        
        .content-section {
            padding: 2rem;
        }
        
        .program-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .program-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .program-header {
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 1rem;
        }
        
        .program-code {
            background: #007bff;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .course-count {
            background: #28a745;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .btn-action {
            margin: 0 0.25rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg" style="background: white; border-bottom: 2px solid #000;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url() ?>" style="color: #333;">
                🎓 LMS
            </a>
            
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, <strong><?= esc($user['name']) ?></strong>
                    <span class="badge bg-danger ms-2">ADMIN</span>
                </span>
                <a class="nav-link" href="<?= base_url('logout') ?>" style="color: #333;">Logout</a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1" style="color: #333; font-weight: 600;">
                        <i class="bi bi-mortarboard"></i> Program Management
                    </h4>
                    <p class="text-muted mb-0">Manage academic programs and their curricula</p>
                </div>
                <div>
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProgramModal">
                        <i class="bi bi-plus-circle"></i> Add New Program
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="content-section">
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

            <!-- Programs List -->
            <?php if (empty($programs)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-mortarboard" style="font-size: 4rem; color: #ccc;"></i>
                    <h5 class="mt-3 mb-2">No Programs Found</h5>
                    <p class="text-muted">Start by creating your first academic program.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProgramModal">
                        <i class="bi bi-plus-circle"></i> Create First Program
                    </button>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($programs as $program): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="program-card">
                                <div class="program-header">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="program-code"><?= esc($program['program_code']) ?></span>
                                        <span class="status-badge status-<?= $program['status'] ?>">
                                            <?= ucfirst($program['status']) ?>
                                        </span>
                                    </div>
                                    <h6 class="mb-1"><?= esc($program['program_name']) ?></h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted"><?= $program['duration_years'] ?> Years</small>
                                        <span class="course-count"><?= $program['course_count'] ?> Courses</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text text-muted small mb-3">
                                        <?= esc($program['description'] ? substr($program['description'], 0, 100) . '...' : 'No description available') ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <?= $program['total_units'] ? $program['total_units'] . ' Units' : 'Units TBD' ?>
                                        </small>
                                        <div>
                                            <a href="<?= base_url('admin/program/' . $program['id'] . '/courses') ?>" 
                                               class="btn btn-sm btn-outline-success btn-action" 
                                               title="Manage Courses">
                                                <i class="bi bi-book"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-primary btn-action" 
                                                    onclick="editProgram(<?= htmlspecialchars(json_encode($program)) ?>)"
                                                    title="Edit Program">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger btn-action" 
                                                    onclick="deleteProgram(<?= $program['id'] ?>, '<?= esc($program['program_name']) ?>')"
                                                    title="Delete Program">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Program Modal -->
    <div class="modal fade" id="createProgramModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border: 2px solid #000; border-radius: 0;">
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333; font-weight: 600;">
                        <i class="bi bi-plus-circle text-primary"></i> Create New Program
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= base_url('admin/programs/create') ?>">
                    <?= csrf_field() ?>
                    <div class="modal-body" style="background: #f8f9fa;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="program_code" class="form-label">Program Code *</label>
                                    <input type="text" class="form-control" id="program_code" name="program_code" 
                                           placeholder="e.g., BSIT" maxlength="20" required>
                                    <div class="form-text">Short code for the program (e.g., BSIT, BSCS)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_years" class="form-label">Duration (Years) *</label>
                                    <select class="form-select" id="duration_years" name="duration_years" required>
                                        <option value="2">2 Years</option>
                                        <option value="3">3 Years</option>
                                        <option value="4" selected>4 Years</option>
                                        <option value="5">5 Years</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="program_name" class="form-label">Program Name *</label>
                            <input type="text" class="form-control" id="program_name" name="program_name" 
                                   placeholder="e.g., Bachelor of Science in Information Technology" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Brief description of the program"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_units" class="form-label">Total Units</label>
                                    <input type="number" class="form-control" id="total_units" name="total_units" 
                                           placeholder="e.g., 120" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="background: white; border-top: 1px solid #ddd;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Program Modal -->
    <div class="modal fade" id="editProgramModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border: 2px solid #000; border-radius: 0;">
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333; font-weight: 600;">
                        <i class="bi bi-pencil text-warning"></i> Edit Program
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= base_url('admin/programs/update') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" id="edit_program_id" name="program_id">
                    <div class="modal-body" style="background: #f8f9fa;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_program_code" class="form-label">Program Code *</label>
                                    <input type="text" class="form-control" id="edit_program_code" name="program_code" 
                                           maxlength="20" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_duration_years" class="form-label">Duration (Years) *</label>
                                    <select class="form-select" id="edit_duration_years" name="duration_years" required>
                                        <option value="2">2 Years</option>
                                        <option value="3">3 Years</option>
                                        <option value="4">4 Years</option>
                                        <option value="5">5 Years</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_program_name" class="form-label">Program Name *</label>
                            <input type="text" class="form-control" id="edit_program_name" name="program_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_total_units" class="form-label">Total Units</label>
                                    <input type="number" class="form-control" id="edit_total_units" name="total_units" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status *</label>
                                    <select class="form-select" id="edit_status" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="background: white; border-top: 1px solid #ddd;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Program Modal -->
    <div class="modal fade" id="deleteProgramModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border: 2px solid #000; border-radius: 0;">
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333; font-weight: 600;">
                        <i class="bi bi-exclamation-triangle text-danger"></i> Delete Program
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background: #f8f9fa;">
                    <p>Are you sure you want to delete the program <strong id="deleteProgramName"></strong>?</p>
                    <p class="text-danger mb-0">
                        <i class="bi bi-exclamation-triangle"></i> 
                        This action cannot be undone. All associated data will be permanently removed.
                    </p>
                </div>
                <div class="modal-footer" style="background: white; border-top: 1px solid #ddd;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteProgram">Delete Program</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Edit program function
        function editProgram(program) {
            document.getElementById('edit_program_id').value = program.id;
            document.getElementById('edit_program_code').value = program.program_code;
            document.getElementById('edit_program_name').value = program.program_name;
            document.getElementById('edit_description').value = program.description || '';
            document.getElementById('edit_duration_years').value = program.duration_years;
            document.getElementById('edit_total_units').value = program.total_units || '';
            document.getElementById('edit_status').value = program.status;
            
            var editModal = new bootstrap.Modal(document.getElementById('editProgramModal'));
            editModal.show();
        }

        // Delete program function
        let programToDelete = null;
        
        function deleteProgram(programId, programName) {
            programToDelete = programId;
            document.getElementById('deleteProgramName').textContent = programName;
            
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteProgramModal'));
            deleteModal.show();
        }
        
        document.getElementById('confirmDeleteProgram').addEventListener('click', function() {
            if (programToDelete) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url("admin/programs/delete") ?>';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '<?= csrf_token() ?>';
                csrfInput.value = '<?= csrf_hash() ?>';
                
                const programIdInput = document.createElement('input');
                programIdInput.type = 'hidden';
                programIdInput.name = 'program_id';
                programIdInput.value = programToDelete;
                
                form.appendChild(csrfInput);
                form.appendChild(programIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Auto-uppercase program code
        document.getElementById('program_code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        document.getElementById('edit_program_code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>