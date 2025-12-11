<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Manage Students' ?></title>
    <!-- Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .student-card {
            transition: transform 0.2s;
        }
        .student-card:hover {
            transform: translateY(-2px);
        }
        .notification-dropdown {
            padding: 0;
        }
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s;
            cursor: pointer;
        }
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        .notification-item.unread {
            background-color: #e7f3ff;
        }
        .notification-item.unread:hover {
            background-color: #d1e8ff;
        }
        .notification-message {
            font-size: 0.9rem;
            margin-bottom: 4px;
            color: #333;
        }
        .notification-time {
            font-size: 0.75rem;
            color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-warning">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="<?= base_url('teacher/dashboard') ?>">
                ITE311 FUNDAR LMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="<?= base_url('teacher/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="<?= base_url('teacher/courses') ?>">My Courses</a>
                    </li>
                </ul>
                
                <!-- Notification Bell Icon -->
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative text-dark" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                            </svg>
                            <span id="notificationBadge" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill" style="display: none; font-size: 0.65rem;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 500px; overflow-y: auto;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fa; padding: 12px 16px;">
                                <span class="fw-bold">Notifications</span>
                                <button class="btn btn-sm btn-link text-decoration-none p-0" id="markAllRead" style="display: none; font-size: 0.85rem;">Mark all as read</button>
                            </div>
                            <div class="dropdown-divider m-0"></div>
                            <div id="notificationList" class="notification-list">
                                <div class="text-center py-4 text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-bell-slash mb-2 opacity-25" viewBox="0 0 16 16">
                                        <path d="M5.164 14H15c-.299-.199-.557-.553-.78-1-.9-1.8-1.22-5.12-1.22-6 0-.264-.02-.523-.06-.776l-.938.938c.02.708.157 2.154.457 3.58.161.767.377 1.566.663 2.258H6.164l-1 1zm5.581-9.91a3.986 3.986 0 0 0-1.948-1.01L8 2.917l-.797.161A4.002 4.002 0 0 0 4 7c0 .628-.134 2.197-.459 3.742-.05.238-.105.479-.166.718l-1.653 1.653c.02-.037.04-.074.059-.113C2.679 11.2 3 7.88 3 7c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0c.942.19 1.788.645 2.457 1.284l-.707.707zM10 15a2 2 0 1 1-4 0h4zm-9.375.625a.53.53 0 0 0 .75.75l14.75-14.75a.53.53 0 0 0-.75-.75L.625 15.625z"/>
                                    </svg>
                                    <p class="mb-0 small">No notifications</p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= esc($user['name']) ?>
                            <span class="badge bg-dark ms-2">TEACHER</span>
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
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('teacher/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('teacher/courses') ?>">My Courses</a></li>
                <li class="breadcrumb-item active"><?= esc($course['title']) ?></li>
            </ol>
        </nav>

        <!-- Page header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h2 class="mb-0"><?= esc($course['title']) ?></h2>
                        <p class="mb-0"><?= esc($course['description'] ?? 'No description available') ?></p>
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

        <!-- Pending Enrollment Requests Section -->
        <?php if (!empty($pendingStudents)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="bi bi-person-plus"></i> Pending Enrollment Requests 
                            <span class="badge bg-danger"><?= count($pendingStudents) ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Students waiting for approval to join this course</p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Request Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingStudents as $student): ?>
                                        <tr>
                                            <td><strong><?= esc($student['student_name']) ?></strong></td>
                                            <td><?= esc($student['student_email']) ?></td>
                                            <td><?= date('M d, Y h:i A', strtotime($student['created_at'])) ?></td>
                                            <td class="text-center">
                                                <form method="POST" action="<?= base_url('teacher/enrollment/approve') ?>" class="d-inline" onsubmit="return confirm('Approve this enrollment request?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="enrollment_id" value="<?= $student['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="<?= base_url('teacher/enrollment/reject') ?>" class="d-inline" onsubmit="return confirm('Reject this enrollment request?');">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="enrollment_id" value="<?= $student['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Enrolled Students Section -->
            <div class="col-md-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Enrolled Students (<?= count($enrolledStudents) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($enrolledStudents)): ?>
                            <div class="alert alert-info mb-0">
                                No students enrolled in this course yet. Add students from the list on the right.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Email</th>
                                            <th>Enrolled Date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($enrolledStudents as $student): ?>
                                            <tr>
                                                <td><strong><?= esc($student['name']) ?></strong></td>
                                                <td><?= esc($student['email']) ?></td>
                                                <td><?= date('M d, Y', strtotime($student['enrollment_date'])) ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="removeStudent(<?= $student['id'] ?>, '<?= esc($student['name']) ?>')">
                                                        Remove
                                                    </button>
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

            <!-- Available Students Section -->
            <div class="col-md-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Add Students</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($availableStudents)): ?>
                            <div class="alert alert-info mb-0">
                                <small>All students are already enrolled in this course.</small>
                            </div>
                        <?php else: ?>
                            <p class="text-muted small mb-3">Available students (<?= count($availableStudents) ?>)</p>
                            <div style="max-height: 400px; overflow-y: auto;">
                                <?php foreach ($availableStudents as $student): ?>
                                    <div class="card student-card mb-2">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?= esc($student['name']) ?></strong>
                                                    <br><small class="text-muted"><?= esc($student['email']) ?></small>
                                                </div>
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="addStudent(<?= $student['id'] ?>, '<?= esc($student['name']) ?>')">
                                                    Add
                                                </button>
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

        <!-- Course Statistics -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title">Course Statistics</h6>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h3 class="text-primary"><?= count($enrolledStudents) ?></h3>
                                <p class="mb-0">Total Enrolled</p>
                            </div>
                            <div class="col-md-4">
                                <h3 class="text-success"><?= count($availableStudents) ?></h3>
                                <p class="mb-0">Available to Add</p>
                            </div>
                            <div class="col-md-4">
                                <h3 class="text-info"><?= count($enrolledStudents) + count($availableStudents) ?></h3>
                                <p class="mb-0">Total Students</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add Student to Course</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('teacher/course/student/add') ?>" method="POST">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="student_id" id="add_student_id">
                    <div class="modal-body">
                        <p>Are you sure you want to add the following student to this course?</p>
                        <p class="text-success"><strong id="add_student_name"></strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Student Modal -->
    <div class="modal fade" id="removeStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Remove Student from Course</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('teacher/course/student/remove') ?>" method="POST">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="student_id" id="remove_student_id">
                    <div class="modal-body">
                        <p>Are you sure you want to remove the following student from this course?</p>
                        <p class="text-danger"><strong id="remove_student_name"></strong></p>
                        <p class="text-muted">This action will remove their enrollment and any associated progress.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Remove Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
    
    <script>
        // Add student function
        function addStudent(studentId, studentName) {
            document.getElementById('add_student_id').value = studentId;
            document.getElementById('add_student_name').textContent = studentName;
            
            var addModal = new bootstrap.Modal(document.getElementById('addStudentModal'));
            addModal.show();
        }

        // Remove student function
        function removeStudent(studentId, studentName) {
            document.getElementById('remove_student_id').value = studentId;
            document.getElementById('remove_student_name').textContent = studentName;
            
            var removeModal = new bootstrap.Modal(document.getElementById('removeStudentModal'));
            removeModal.show();
        }
    </script>
</body>
</html>
