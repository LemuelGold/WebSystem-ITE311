<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Manage Students' ?></title>
    <!-- Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .student-card {
            transition: transform 0.2s;
        }
        .student-card:hover {
            transform: translateY(-2px);
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
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text text-dark">
                            <?= esc($user['name']) ?>
                            <span class="badge bg-dark ms-2">TEACHER</span>
                        </span>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
