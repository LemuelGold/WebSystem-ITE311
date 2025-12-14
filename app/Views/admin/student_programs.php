<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
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
        
        .status-active { background-color: #28a745 !important; }
        .status-inactive { background-color: #6c757d !important; }
        .status-graduated { background-color: #17a2b8 !important; }
        .status-dropped { background-color: #dc3545 !important; }
        
        .table th {
            border-top: none;
            font-weight: 600;
        }
        
        .student-row:hover {
            background-color: #f8f9fa;
        }
        
        .unenrolled-section {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .unenrolled-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 0.75rem;
            transition: all 0.2s ease;
        }
        
        .unenrolled-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }
        
        .section-header {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
        }
        
        .search-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('admin/dashboard') ?>">LMS</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/users') ?>">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/courses') ?>">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('admin/student-programs') ?>">Student Programs</a>
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

    <div class="container mt-4">
        <!-- Page header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card" style="border: 2px solid #000;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0 text-dark">Student Program Enrollments</h2>
                            <p class="text-muted mb-0">Manage student enrollments in academic programs</p>
                        </div>
                        <div>
                            <button class="btn btn-outline-dark me-2" onclick="cleanupEnrollments()" title="Remove enrollments from students not in any program">
                                <i class="bi bi-trash"></i> Cleanup Enrollments
                            </button>
                            <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">
                                <i class="bi bi-plus-circle"></i> Enroll Student
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-section">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" id="studentSearch" class="form-control" placeholder="Search students by name, email, program, or student number...">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="programFilter">
                        <option value="">All Programs</option>
                        <?php if (!empty($programs)): ?>
                            <?php foreach ($programs as $program): ?>
                                <option value="<?= esc($program['program_code']) ?>"><?= esc($program['program_code']) ?> - <?= esc($program['program_name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
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

        <!-- Statistics Section -->
        <?php 
        $enrolledCount = count(array_filter($students, function($s) { return !empty($s['program_id']); }));
        $unenrolledCount = count($unenrolledStudents ?? []);
        $totalStudents = $enrolledCount + $unenrolledCount;
        ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <h4 class="mb-1 text-primary"><?= $totalStudents ?></h4>
                    <small class="text-muted">Total Students</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h4 class="mb-1 text-success"><?= $enrolledCount ?></h4>
                    <small class="text-muted">Enrolled in Programs</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h4 class="mb-1 text-warning"><?= $unenrolledCount ?></h4>
                    <small class="text-muted">Awaiting Enrollment</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h4 class="mb-1 text-info"><?= count($programs ?? []) ?></h4>
                    <small class="text-muted">Available Programs</small>
                </div>
            </div>
        </div>

        <!-- Unenrolled Students Section -->
        <?php if (!empty($unenrolledStudents)): ?>
            <div class="unenrolled-section">
                <div class="mb-3">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle text-warning"></i> Students Awaiting Program Enrollment
                        <span class="badge bg-warning text-dark ms-2"><?= count($unenrolledStudents) ?></span>
                    </h5>
                </div>
                <p class="text-muted mb-3">These students need to be enrolled in academic programs to access courses:</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-warning">
                            <tr>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Joined Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unenrolledStudents as $student): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($student['name']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?= esc($student['email']) ?></span>
                                    </td>
                                    <td>
                                        <small class="text-info">
                                            <i class="bi bi-calendar"></i> <?= date('M d, Y', strtotime($student['created_at'] ?? 'now')) ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="enrollStudent(<?= $student['id'] ?>, '<?= esc($student['name']) ?>')"
                                                title="Enroll in Program">
                                            <i class="bi bi-plus-circle"></i> Enroll
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Enrolled Students Section -->
        <div class="section-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people text-primary"></i> Enrolled Students
                    <span class="badge bg-primary ms-2"><?= $enrolledCount ?></span>
                </h5>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="graduated">Graduated</option>
                        <option value="dropped">Dropped</option>
                    </select>
                    <select class="form-select form-select-sm" id="yearFilter" style="width: auto;">
                        <option value="">All Years</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Students table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Enrolled Students (<?= $enrolledCount ?>)</h5>
                
                <?php if (empty($students) || $enrolledCount == 0): ?>
                    <div class="alert alert-info">
                        No students enrolled in programs yet. Click "Enroll Student" to add one.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover" id="studentsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Program</th>
                                    <th>Year & Semester</th>
                                    <th>Academic Year</th>
                                    <th>Student ID</th>
                                    <th>Status</th>
                                    <th>Enrolled Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <?php if (!empty($student['program_id'])): ?>
                                        <tr class="student-row" 
                                            data-program="<?= esc($student['program_code']) ?>" 
                                            data-status="<?= esc($student['enrollment_status']) ?>"
                                            data-year="<?= esc($student['current_year_level']) ?>"
                                            data-search="<?= strtolower(esc($student['name'] . ' ' . $student['email'] . ' ' . $student['program_code'] . ' ' . ($student['student_number'] ?? ''))) ?>">
                                            <td>
                                                <div>
                                                    <strong><?= esc($student['name']) ?></strong>
                                                    <br><small class="text-muted"><?= esc($student['email']) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary mb-1"><?= esc($student['program_code']) ?></span>
                                                <br><small class="text-muted"><?= esc($student['program_name']) ?></small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <span class="badge bg-info">Year <?= $student['current_year_level'] ?></span>
                                                    <span class="badge bg-success"><?= substr($student['current_semester'], 0, 3) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <strong><?= $student['academic_year'] ?></strong>
                                            </td>
                                            <td>
                                                <?php if ($student['student_number']): ?>
                                                    <span class="text-info fw-medium"><?= esc($student['student_number']) ?></span>
                                                <?php else: ?>
                                                    <small class="text-muted">Not set</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge status-<?= esc($student['enrollment_status']) ?>">
                                                    <?= ucfirst($student['enrollment_status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?= date('M d, Y', strtotime($student['created_at'] ?? 'now')) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="updateStudent(<?= htmlspecialchars(json_encode($student)) ?>)"
                                                        title="Update Enrollment">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Enroll Student Modal -->
    <div class="modal fade" id="enrollStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border: 2px solid #000; border-radius: 0;">
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333; font-weight: 600;">
                        <i class="bi bi-plus-circle text-success"></i> Enroll Student in Program
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= base_url('admin/student-programs/enroll') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" id="enroll_student_id" name="student_id">
                    <div class="modal-body" style="background: #f8f9fa;">
                        <div class="mb-3">
                            <label class="form-label">Student *</label>
                            <input type="text" class="form-control" id="enroll_student_search" placeholder="Search for a student by name or email..." autocomplete="off">
                            <div id="student_search_results" class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto; display: none;"></div>
                            <input type="hidden" id="enroll_student_name" name="selected_student_name">
                        </div>
                        <div class="mb-3">
                            <label for="program_id" class="form-label">Program *</label>
                            <select class="form-select" id="program_id" name="program_id" required>
                                <option value="">Select a program...</option>
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?= $program['id'] ?>">
                                        <?= esc($program['program_code']) ?> - <?= esc($program['program_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="year_level" class="form-label">Starting Year Level *</label>
                                    <select class="form-select" id="year_level" name="year_level" required>
                                        <option value="1">1st Year</option>
                                        <option value="2">2nd Year</option>
                                        <option value="3">3rd Year</option>
                                        <option value="4">4th Year</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Starting Semester *</label>
                                    <select class="form-select" id="semester" name="semester" required>
                                        <option value="1st Semester">1st Semester</option>
                                        <option value="2nd Semester">2nd Semester</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="academic_year" class="form-label">Academic Year *</label>
                            <input type="text" class="form-control" id="academic_year" name="academic_year" 
                                   value="<?= date('Y') ?>" placeholder="e.g., 2025" required>
                        </div>
                    </div>
                    <div class="modal-footer" style="background: white; border-top: 1px solid #ddd;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Enroll Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Student Modal -->
    <div class="modal fade" id="updateStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border: 2px solid #000; border-radius: 0;">
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333; font-weight: 600;">
                        <i class="bi bi-pencil text-warning"></i> Update Student Enrollment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= base_url('admin/student-programs/update') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" id="update_student_id" name="student_id">
                    <div class="modal-body" style="background: #f8f9fa;">
                        <div class="mb-3">
                            <label class="form-label">Student</label>
                            <input type="text" class="form-control" id="update_student_info" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="update_year_level" class="form-label">Current Year Level *</label>
                                    <select class="form-select" id="update_year_level" name="year_level" required>
                                        <option value="1">1st Year</option>
                                        <option value="2">2nd Year</option>
                                        <option value="3">3rd Year</option>
                                        <option value="4">4th Year</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="update_semester" class="form-label">Current Semester *</label>
                                    <select class="form-select" id="update_semester" name="semester" required>
                                        <option value="1st Semester">1st Semester</option>
                                        <option value="2nd Semester">2nd Semester</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="update_academic_year" class="form-label">Academic Year *</label>
                            <input type="text" class="form-control" id="update_academic_year" name="academic_year" required>
                        </div>
                        <div class="mb-3">
                            <label for="update_status" class="form-label">Status *</label>
                            <select class="form-select" id="update_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="graduated">Graduated</option>
                                <option value="dropped">Dropped</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer" style="background: white; border-top: 1px solid #ddd;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Enrollment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Search and filter functionality - fixed
        document.addEventListener('DOMContentLoaded', function() {
            const studentSearchInput = document.getElementById('studentSearch');
            if (studentSearchInput) {
                // Remove any readonly or disabled attributes
                studentSearchInput.removeAttribute('readonly');
                studentSearchInput.removeAttribute('disabled');
                
                // Add search functionality
                studentSearchInput.addEventListener('keyup', function() {
                    filterStudents();
                });
                
                // Also add input event for better responsiveness
                studentSearchInput.addEventListener('input', function() {
                    filterStudents();
                });
            }
        });
        
        document.getElementById('programFilter').addEventListener('change', function() {
            filterStudents();
        });
        
        document.getElementById('statusFilter').addEventListener('change', function() {
            filterStudents();
        });
        
        document.getElementById('yearFilter').addEventListener('change', function() {
            filterStudents();
        });
        
        function filterStudents() {
            const searchTerm = document.getElementById('studentSearch').value.toLowerCase();
            const programFilter = document.getElementById('programFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const yearFilter = document.getElementById('yearFilter').value;
            
            const studentRows = document.querySelectorAll('.student-row');
            let visibleCount = 0;
            
            studentRows.forEach(function(row) {
                const searchData = row.getAttribute('data-search');
                const program = row.getAttribute('data-program');
                const status = row.getAttribute('data-status');
                const year = row.getAttribute('data-year');
                
                let show = true;
                
                // Search filter
                if (searchTerm && !searchData.includes(searchTerm)) {
                    show = false;
                }
                
                // Program filter
                if (programFilter && program !== programFilter) {
                    show = false;
                }
                
                // Status filter
                if (statusFilter && status !== statusFilter) {
                    show = false;
                }
                
                // Year filter
                if (yearFilter && year !== yearFilter) {
                    show = false;
                }
                
                if (show) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update count in card title
            const cardTitle = document.querySelector('.card-title');
            if (cardTitle) {
                cardTitle.textContent = `Enrolled Students (${visibleCount})`;
            }
        }

        // Enroll student function
        function enrollStudent(studentId, studentName) {
            document.getElementById('enroll_student_id').value = studentId;
            document.getElementById('enroll_student_name').value = studentName;
            document.getElementById('enroll_student_search').value = studentName;
            
            var enrollModal = new bootstrap.Modal(document.getElementById('enrollStudentModal'));
            enrollModal.show();
        }

        // Student search functionality for enrollment modal
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('enroll_student_search');
            const resultsDiv = document.getElementById('student_search_results');
            const hiddenStudentId = document.getElementById('enroll_student_id');
            const hiddenStudentName = document.getElementById('enroll_student_name');

            if (searchInput && resultsDiv) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.trim();
                    
                    if (searchTerm.length < 2) {
                        resultsDiv.style.display = 'none';
                        return;
                    }

                    // Get students from "Students Awaiting Program Enrollment" section (unenrolled students only)
                    const waitingStudentsTable = document.querySelector('.table-warning');
                    const results = [];
                    
                    if (waitingStudentsTable) {
                        const tableBody = waitingStudentsTable.closest('table').querySelector('tbody');
                        if (tableBody) {
                            const waitingRows = tableBody.querySelectorAll('tr');
                            
                            waitingRows.forEach(function(row) {
                                const studentNameElement = row.querySelector('td:first-child strong');
                                const studentEmailElement = row.querySelector('td:nth-child(2) .text-muted');
                                const enrollButton = row.querySelector('button[onclick*="enrollStudent"]');
                                
                                if (studentNameElement && studentEmailElement && enrollButton) {
                                    const studentName = studentNameElement.textContent.trim();
                                    const studentEmail = studentEmailElement.textContent.trim();
                                    const studentId = enrollButton.getAttribute('onclick')?.match(/enrollStudent\((\d+)/)?.[1];
                                    
                                    // Search in name and email
                                    const searchText = (studentName + ' ' + studentEmail).toLowerCase();
                                    if (searchText.includes(searchTerm.toLowerCase())) {
                                        results.push({
                                            id: studentId,
                                            name: studentName,
                                            email: studentEmail
                                        });
                                    }
                                }
                            });
                        }
                    }

                    // Display results
                    if (results.length > 0) {
                        let html = '';
                        results.forEach(function(student) {
                            html += `<a href="#" class="dropdown-item" onclick="selectStudent(${student.id}, '${student.name.replace(/'/g, "\\'")}'); return false;">
                                        <strong>${student.name}</strong><br>
                                        <small class="text-muted">${student.email}</small>
                                     </a>`;
                        });
                        resultsDiv.innerHTML = html;
                        resultsDiv.style.display = 'block';
                    } else {
                        resultsDiv.innerHTML = '<div class="dropdown-item text-muted">No students found</div>';
                        resultsDiv.style.display = 'block';
                    }
                });

                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                        resultsDiv.style.display = 'none';
                    }
                });
            }
        });

        // Select student from search results
        function selectStudent(studentId, studentName) {
            document.getElementById('enroll_student_id').value = studentId;
            document.getElementById('enroll_student_name').value = studentName;
            document.getElementById('enroll_student_search').value = studentName;
            document.getElementById('student_search_results').style.display = 'none';
        }

        // Update student function
        function updateStudent(student) {
            document.getElementById('update_student_id').value = student.id;
            document.getElementById('update_student_info').value = student.name + ' (' + student.program_code + ')';
            document.getElementById('update_year_level').value = student.current_year_level;
            document.getElementById('update_semester').value = student.current_semester;
            document.getElementById('update_academic_year').value = student.academic_year;
            document.getElementById('update_status').value = student.enrollment_status;
            
            var updateModal = new bootstrap.Modal(document.getElementById('updateStudentModal'));
            updateModal.show();
        }



        // Cleanup enrollments function
        function cleanupEnrollments() {
            if (confirm('This will remove all course enrollments from students who are not enrolled in any academic program. Are you sure you want to continue?')) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url("admin/cleanup-unprogrammed-enrollments") ?>';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '<?= csrf_token() ?>';
                csrfInput.value = '<?= csrf_hash() ?>';
                
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>