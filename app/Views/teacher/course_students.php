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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: white !important;
            min-height: 100vh;
        }
        
        .navbar {
            background-color: white !important;
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
        
        .page-header {
            background: white;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .page-header h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .content-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .content-card .card-header {
            background: white;
            border-bottom: 2px solid #000;
            padding: 1rem 1.5rem;
        }
        
        .content-card .card-header h5 {
            color: #333;
            font-weight: 600;
            margin: 0;
        }
        
        .student-card {
            transition: transform 0.2s;
            border: 1px solid #eee;
        }
        
        .student-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .btn-outline-dark {
            border-color: #333;
            color: #333;
        }
        
        .btn-outline-dark:hover {
            background-color: #333;
            border-color: #333;
            color: white;
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
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <span class="navbar-brand fw-bold text-dark" style="cursor: default;">
                LMS
            </span>
            
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
        <!-- Page header -->
        <div class="page-header">
            <h2>
                <?= esc($course['title']) ?>
                <?php if (!empty($course['section'])): ?>
                    <span class="badge bg-primary ms-2">Section <?= esc($course['section']) ?></span>
                <?php endif; ?>
            </h2>
            <p class="text-muted mb-1"><?= esc($course['description'] ?? 'No description available') ?></p>
            <?php if (!empty($course['schedule_time']) || !empty($course['room'])): ?>
                <p class="text-muted mb-0 small">
                    <?php if (!empty($course['schedule_time'])): ?>
                        <i class="bi bi-clock"></i> <?= esc($course['schedule_time']) ?>
                    <?php endif; ?>
                    <?php if (!empty($course['room'])): ?>
                        <i class="bi bi-geo-alt ms-2"></i> <?= esc($course['room']) ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
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
                <div class="content-card">
                    <div class="card-header">
                        <h5>
                            <i class="bi bi-hourglass-split"></i> Pending Enrollment Requests 
                            <span class="badge bg-info"><?= count($pendingStudents) ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Waiting for students to accept your enrollment invitations</p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Request Date</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingStudents as $student): ?>
                                        <tr>
                                            <td><strong><?= esc($student['student_name']) ?></strong></td>
                                            <td><?= esc($student['student_email']) ?></td>
                                            <td><?= date('M d, Y h:i A', strtotime($student['created_at'])) ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock"></i> Waiting for Response
                                                </span>
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
                <div class="content-card">
                    <div class="card-header">
                        <h5>Enrolled Students</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($enrolledStudents)): ?>
                            <div class="alert alert-info mb-0">
                                No students enrolled in this course yet. Add students from the list on the right.
                            </div>
                        <?php else: ?>
                            <!-- Search bar for enrolled students -->
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-sm" id="enrolledStudentsSearch" 
                                       placeholder="Search enrolled students by name or email...">
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover" id="enrolledStudentsTable">
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
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="removeStudent(<?= $student['id'] ?>, '<?= esc($student['name']) ?>')">
                                                        <i class="bi bi-trash"></i>
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
                <div class="content-card">
                    <div class="card-header">
                        <h5>Add Students</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($availableStudents)): ?>
                            <div class="alert alert-info mb-3">
                                <small>No students available to add. Students must be enrolled in a program that contains this course and not already enrolled in this course.</small>
                            </div>
                            
                            <!-- Debug Information -->
                            <?php if (isset($debug_info)): ?>
                                <div class="alert alert-warning">
                                    <strong>Debug Info:</strong><br>
                                    <small>
                                        Course ID: <?= $debug_info['course_id'] ?><br>
                                        Course Code: <?= $debug_info['course_code'] ?><br>
                                        Programs with this course: <?= count($debug_info['program_courses']) ?><br>
                                        <?php if (!empty($debug_info['program_courses'])): ?>
                                            Programs: <?php foreach($debug_info['program_courses'] as $pc): ?>
                                                <?= $pc['program_code'] ?> (Year <?= $pc['year_level'] ?>, <?= $pc['semester'] ?>)
                                            <?php endforeach; ?><br>
                                        <?php endif; ?>
                                        All courses with same code in programs: <?= count($debug_info['all_same_course_programs']) ?><br>
                                        <?php if (!empty($debug_info['all_same_course_programs'])): ?>
                                            <?php foreach($debug_info['all_same_course_programs'] as $pc): ?>
                                                Course ID <?= $pc['course_id'] ?> (Section: <?= $pc['section'] ?: 'None' ?>) in <?= $pc['program_code'] ?><br>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        Students in programs: <?= count($debug_info['all_program_students']) ?><br>
                                        Already enrolled: <?= count($debug_info['active_enrollments']) ?><br>
                                        Same course sections: <?= count($debug_info['same_course_ids']) ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Section Selection -->
                            <?php if (!empty($otherSections)): ?>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Select Section to Add Students To:</label>
                                    <select class="form-select form-select-sm" id="targetSectionSelect">
                                        <option value="<?= $course['id'] ?>">
                                            Current Section: <?= !empty($course['section']) ? esc($course['section']) : 'No Section' ?>
                                            <?php if (!empty($course['schedule_time'])): ?>
                                                (<?= esc($course['schedule_time']) ?>)
                                            <?php endif; ?>
                                        </option>
                                        <?php foreach ($otherSections as $section): ?>
                                            <option value="<?= $section['id'] ?>">
                                                Section: <?= !empty($section['section']) ? esc($section['section']) : 'No Section' ?>
                                                <?php if (!empty($section['schedule_time'])): ?>
                                                    (<?= esc($section['schedule_time']) ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            
                            <p class="text-muted small mb-2">Available students (<?= count($availableStudents) ?>)</p>
                            <!-- Search bar for available students -->
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-sm" id="availableStudentsSearch" 
                                       placeholder="Search available students by name or email...">
                            </div>
                            <div style="max-height: 400px; overflow-y: auto;" id="availableStudentsList">
                                <?php foreach ($availableStudents as $student): ?>
                                    <div class="card student-card mb-2">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?= esc($student['name']) ?></strong>
                                                    <br><small class="text-muted"><?= esc($student['email']) ?></small>
                                                    <br>
                                                    <span class="badge bg-primary me-1"><?= esc($student['program_code']) ?></span>
                                                    <span class="badge bg-info">Year <?= esc($student['current_year_level']) ?></span>
                                                    <span class="badge bg-secondary ms-1"><?= esc($student['current_semester']) ?></span>
                                                </div>
                                                <button class="btn btn-sm btn-outline-dark" 
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
                <div class="content-card">
                    <div class="card-header">
                        <h5>Course Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h3 class="text-dark"><?= count($enrolledStudents) ?></h3>
                                <p class="mb-0 text-muted">Total Enrolled</p>
                            </div>
                            <div class="col-md-4">
                                <h3 class="text-dark"><?= count($availableStudents) ?></h3>
                                <p class="mb-0 text-muted">Available to Add</p>
                            </div>
                            <div class="col-md-4">
                                <h3 class="text-dark"><?= count($enrolledStudents) + count($availableStudents) ?></h3>
                                <p class="mb-0 text-muted">Total Students</p>
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
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333;">Add Student to Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('teacher/course/student/add') ?>" method="POST">
                    <input type="hidden" name="course_id" id="add_course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="user_id" id="add_student_id">
                    <div class="modal-body">
                        <p>Are you sure you want to add the following student to this course?</p>
                        <p class="text-success"><strong id="add_student_name"></strong></p>
                        <p class="text-muted small">
                            <strong>Section:</strong> <span id="add_section_info">
                                <?= !empty($course['section']) ? 'Section ' . esc($course['section']) : 'No Section' ?>
                            </span>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Student Modal -->
    <div class="modal fade" id="removeStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333;">Remove Student from Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('teacher/course/student/remove') ?>" method="POST">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="user_id" id="remove_student_id">
                    <div class="modal-body">
                        <p>Are you sure you want to remove the following student from this course?</p>
                        <p class="text-danger"><strong id="remove_student_name"></strong></p>
                        <p class="text-muted">This action will remove their enrollment and any associated progress.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-outline-danger">Remove Student</button>
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
            
            // Update course_id and section info based on selected section
            const targetSectionSelect = document.getElementById('targetSectionSelect');
            if (targetSectionSelect) {
                document.getElementById('add_course_id').value = targetSectionSelect.value;
                const selectedOption = targetSectionSelect.options[targetSectionSelect.selectedIndex];
                document.getElementById('add_section_info').textContent = selectedOption.text.replace('Current Section: ', '').replace('Section: ', '');
            }
            
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

        // Search functionality for enrolled students table
        document.addEventListener('DOMContentLoaded', function() {
            const enrolledSearch = document.getElementById('enrolledStudentsSearch');
            const enrolledTable = document.getElementById('enrolledStudentsTable');
            
            if (enrolledSearch && enrolledTable) {
                enrolledSearch.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();
                    const tableRows = enrolledTable.querySelectorAll('tbody tr');
                    
                    tableRows.forEach(function(row) {
                        const studentName = row.cells[0].textContent.toLowerCase();
                        const studentEmail = row.cells[1].textContent.toLowerCase();
                        
                        if (studentName.includes(searchTerm) || studentEmail.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Search functionality for available students list - isolated fix
            setTimeout(function() {
                const availableSearch = document.getElementById('availableStudentsSearch');
                const availableList = document.getElementById('availableStudentsList');
                
                if (availableSearch && availableList) {
                    // Remove any existing event listeners
                    availableSearch.removeAttribute('readonly');
                    availableSearch.removeAttribute('disabled');
                    
                    // Add the search functionality
                    availableSearch.oninput = function() {
                        const searchTerm = this.value.toLowerCase().trim();
                        const studentCards = availableList.querySelectorAll('.student-card');
                        
                        for (let i = 0; i < studentCards.length; i++) {
                            const card = studentCards[i];
                            const cardText = card.textContent.toLowerCase();
                            
                            if (searchTerm === '' || cardText.includes(searchTerm)) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        }
                    };
                }
            }, 500);
        });
    </script>
</body>
</html>
