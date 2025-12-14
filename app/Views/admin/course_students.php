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
            max-width: 1200px;
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
        
        .course-info {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        
        .search-section {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .students-table {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #333;
        }
        
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .enrollment-badge {
            font-size: 0.8rem;
        }
        
        .no-students {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .back-button {
            background: #6c757d;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .back-button:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg" style="background: white; border-bottom: 2px solid #000;">
        <div class="container">
            <span class="navbar-brand fw-bold" style="color: #333; cursor: default;">
                🎓 LMS
            </span>
            
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
                        <i class="bi bi-people"></i> Course Students
                    </h4>
                    <p class="text-muted mb-0">View and manage enrolled students</p>
                </div>
                <a href="<?= base_url('admin/courses') ?>" class="back-button">
                    <i class="bi bi-arrow-left"></i> Back to Courses
                </a>
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

            <!-- Section Tabs -->
            <?php if (count($allSections) > 1): ?>
            <div class="mb-3">
                <div class="d-flex align-items-center mb-2">
                    <h6 class="mb-0 me-3" style="color: #333;">Sections:</h6>
                    <div class="btn-group" role="group">
                        <?php foreach ($allSections as $section): ?>
                            <a href="<?= base_url("admin/course/{$section['id']}/students") ?>" 
                               class="btn btn-sm <?= $section['id'] == $currentCourseId ? 'btn-primary' : 'btn-outline-primary' ?>">
                                <?= !empty($section['section']) ? 'Section ' . esc($section['section']) : 'Main' ?>
                                <span class="badge bg-light text-dark ms-1"><?= $section['enrolled_count'] ?></span>
                            </a>
                        <?php endforeach; ?>
                        <button class="btn btn-sm btn-success" onclick="addSection(<?= htmlspecialchars(json_encode($course), ENT_QUOTES, 'UTF-8') ?>)">
                            <i class="bi bi-plus"></i> Add Section
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Course Information -->
            <div class="course-info">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="mb-2" style="color: #333; font-weight: 600;">
                            <?= esc($course['title']) ?>
                            <?php if (!empty($course['section'])): ?>
                                <span class="badge bg-secondary ms-2">Section <?= esc($course['section']) ?></span>
                            <?php endif; ?>
                        </h5>
                        <p class="text-muted mb-2">
                            <strong>Course ID:</strong> <?= esc($course['course_code'] ?? $course['id']) ?>
                            <?php if (!empty($course['schedule_time'])): ?>
                                | <strong>Schedule:</strong> <?= esc($course['schedule_time']) ?>
                            <?php endif; ?>
                            <?php if (!empty($course['room'])): ?>
                                | <strong>Room:</strong> <?= esc($course['room']) ?>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($course['description'])): ?>
                            <p class="text-muted mb-2"><?= esc($course['description']) ?></p>
                        <?php endif; ?>
                        <p class="text-muted mb-0">
                            <strong>Instructor:</strong> <?= esc($course['instructor_name'] ?? 'Unassigned') ?>
                            <?php if (!empty($course['instructor_email'])): ?>
                                (<?= esc($course['instructor_email']) ?>)
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($course['units'])): ?>
                            <p class="text-muted mb-0">
                                <strong>Units:</strong> <?= esc($course['units']) ?> Unit<?= $course['units'] > 1 ? 's' : '' ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($course['term'])): ?>
                            <p class="text-muted mb-0">
                                <strong>Term:</strong> <?= esc($course['term']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-number"><?= $totalEnrolled ?></div>
                                    <div class="text-muted">Enrolled</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-number text-warning"><?= $pendingEnrollments ?></div>
                                    <div class="text-muted">Pending</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Add Section Button -->
                        <div class="mt-3">
                            <button class="btn btn-success btn-sm w-100" onclick="addSection(<?= htmlspecialchars(json_encode($course), ENT_QUOTES, 'UTF-8') ?>)">
                                <i class="bi bi-plus-circle"></i> Add Another Section
                            </button>
                            <small class="text-muted d-block mt-1 text-center">Create another section of this course with different schedule</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Enrollments Section -->
            <?php if (!empty($pendingEnrollmentsList)): ?>
                <div class="search-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0" style="color: #333; font-weight: 600;">
                            <i class="bi bi-clock-history text-warning"></i> Pending Enrollment Requests
                            <span class="badge bg-warning text-dark ms-2"><?= count($pendingEnrollmentsList) ?></span>
                        </h6>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Email</th>
                                    <th>Request Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingEnrollmentsList as $pending): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="student-avatar me-3" style="background: #ffc107;">
                                                    <?= strtoupper(substr($pending['student_name'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <strong><?= esc($pending['student_name']) ?></strong>
                                                    <br><small class="text-muted">ID: <?= esc($pending['student_id']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted"><?= esc($pending['student_email']) ?></span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                <?= date('M d, Y', strtotime($pending['created_at'])) ?>
                                                <br><small><?= date('g:i A', strtotime($pending['created_at'])) ?></small>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="approveEnrollment(<?= $pending['id'] ?>, '<?= esc($pending['student_name']) ?>')"
                                                        title="Approve Enrollment">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="rejectEnrollment(<?= $pending['id'] ?>, '<?= esc($pending['student_name']) ?>')"
                                                        title="Reject Enrollment">
                                                    <i class="bi bi-x-lg"></i> Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Search Section -->
            <div class="search-section">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0" style="color: #333; font-weight: 600;">
                            <i class="bi bi-search"></i> Search Enrolled Students
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="studentSearch" placeholder="Search by name or email...">
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="students-table">
                <?php if (!empty($enrolledStudents)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="studentsTable">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Email</th>
                                    <th>Enrollment Date</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrolledStudents as $student): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="student-avatar me-3">
                                                    <?= strtoupper(substr($student['student_name'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <strong><?= esc($student['student_name']) ?></strong>
                                                    <br><small class="text-muted">ID: <?= esc($student['student_id']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted"><?= esc($student['student_email']) ?></span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                <?= date('M d, Y', strtotime($student['created_at'])) ?>
                                                <br><small><?= date('g:i A', strtotime($student['created_at'])) ?></small>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success enrollment-badge">
                                                <i class="bi bi-check-circle"></i> Enrolled
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="removeStudent(<?= $student['student_id'] ?>, '<?= esc($student['student_name']) ?>')"
                                                    title="Remove from course">
                                                <i class="bi bi-person-x"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-students">
                        <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3 mb-2">No Students Enrolled</h5>
                        <p class="text-muted">This course doesn't have any enrolled students yet. Use the invite feature below to add students.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Invite Students Section -->
            <div class="search-section mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0" style="color: #333; font-weight: 600;">
                        <i class="bi bi-person-plus text-success"></i> Invite Students to Course
                    </h6>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <?php 
                        // Get students enrolled in programs that contain this specific course
                        $studentsBuilder = \Config\Database::connect()->table('users');
                        $allStudents = $studentsBuilder
                            ->select('users.id, users.name, users.email, programs.program_code, programs.program_name')
                            ->join('student_programs', 'student_programs.student_id = users.id', 'inner')
                            ->join('programs', 'programs.id = student_programs.program_id', 'inner')
                            ->join('program_courses', 'program_courses.program_id = programs.id', 'inner')
                            ->where('users.role', 'student')
                            ->where('users.deleted_at IS NULL')
                            ->where('student_programs.status', 'active')
                            ->where('program_courses.course_id', $course['id'])
                            ->whereNotIn('users.id', function($builder) use ($course) {
                                return $builder->select('user_id')
                                              ->from('enrollments')
                                              ->where('course_id', $course['id'])
                                              ->whereIn('status', ['pending', 'approved', 'confirmed']);
                            })
                            ->orderBy('users.name', 'ASC')
                            ->get()
                            ->getResultArray();
                        
                        // Remove duplicates by both user ID and email (in case there are duplicate user records)
                        $availableStudents = [];
                        $seenKeys = [];
                        foreach ($allStudents as $student) {
                            $uniqueKey = $student['id'] . '|' . $student['email'];
                            if (!in_array($uniqueKey, $seenKeys)) {
                                $availableStudents[] = $student;
                                $seenKeys[] = $uniqueKey;
                            }
                        }
                        
                        // Additional deduplication by email only (in case there are different IDs with same email)
                        $finalStudents = [];
                        $seenEmails = [];
                        foreach ($availableStudents as $student) {
                            if (!in_array($student['email'], $seenEmails)) {
                                $finalStudents[] = $student;
                                $seenEmails[] = $student['email'];
                            }
                        }
                        $availableStudents = $finalStudents;
                        ?>
                        
                        <?php if (empty($availableStudents)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>No students available to invite.</strong><br>
                                <small>Students must be enrolled in a program that contains this course and not already enrolled in this course.</small>
                            </div>
                        <?php else: ?>
                            <select class="form-select" id="studentSelect">
                                <option value="">-- Select Student to Invite --</option>
                                <?php foreach ($availableStudents as $student): ?>
                                    <option value="<?= $student['id'] ?>">
                                        <?= esc($student['name']) ?> (<?= esc($student['email']) ?>) - <?= esc($student['program_code']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($availableStudents)): ?>
                    <div class="col-md-4">
                        <button class="btn btn-success" onclick="inviteStudent()" id="inviteBtn" disabled>
                            <i class="bi bi-send"></i> Send Invitation
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Invite Student Modal -->
    <div class="modal fade" id="inviteStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border: 2px solid #000; border-radius: 0;">
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333; font-weight: 600;">
                        <i class="bi bi-person-plus text-success"></i> Invite Student
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background: #f8f9fa;">
                    <p>Are you sure you want to invite <strong id="studentNameToInvite"></strong> to this course?</p>
                    <p class="text-muted mb-0">The student will receive a notification and can accept or decline the invitation.</p>
                </div>
                <div class="modal-footer" style="background: white; border-top: 1px solid #ddd;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmInviteStudent">Send Invitation</button>
                </div>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>

    <!-- Remove Student Modal -->
    <div class="modal fade" id="removeStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border: 2px solid #000; border-radius: 0;">
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333; font-weight: 600;">
                        <i class="bi bi-exclamation-triangle text-warning"></i> Remove Student
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background: #f8f9fa;">
                    <p>Are you sure you want to remove <strong id="studentNameToRemove"></strong> from this course?</p>
                    <p class="text-muted mb-0">This action cannot be undone. The student will lose access to course materials and their enrollment record will be deleted.</p>
                </div>
                <div class="modal-footer" style="background: white; border-top: 1px solid #ddd;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemoveStudent">Remove Student</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Search functionality for students table
        document.getElementById('studentSearch').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#studentsTable tbody tr');
            
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

        // Enrollment approval/rejection functionality
        function approveEnrollment(enrollmentId, studentName) {
            if (confirm(`Are you sure you want to approve enrollment for ${studentName}?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url("admin/enrollment/approve") ?>';
                
                const enrollmentIdInput = document.createElement('input');
                enrollmentIdInput.type = 'hidden';
                enrollmentIdInput.name = 'enrollment_id';
                enrollmentIdInput.value = enrollmentId;
                
                form.appendChild(enrollmentIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function rejectEnrollment(enrollmentId, studentName) {
            if (confirm(`Are you sure you want to reject enrollment for ${studentName}? This will count as one attempt.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url("admin/enrollment/reject") ?>';
                
                const enrollmentIdInput = document.createElement('input');
                enrollmentIdInput.type = 'hidden';
                enrollmentIdInput.name = 'enrollment_id';
                enrollmentIdInput.value = enrollmentId;
                
                form.appendChild(enrollmentIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Invite student functionality
        let studentToInvite = null;
        
        function inviteStudent() {
            const studentSelect = document.getElementById('studentSelect');
            const selectedStudentId = studentSelect.value;
            const selectedStudentName = studentSelect.options[studentSelect.selectedIndex].text;
            
            if (!selectedStudentId) {
                alert('Please select a student to invite.');
                return;
            }
            
            studentToInvite = selectedStudentId;
            document.getElementById('studentNameToInvite').textContent = selectedStudentName;
            
            const inviteModal = new bootstrap.Modal(document.getElementById('inviteStudentModal'));
            inviteModal.show();
        }
        
        // Enable/disable invite button based on selection
        document.getElementById('studentSelect').addEventListener('change', function() {
            const inviteBtn = document.getElementById('inviteBtn');
            inviteBtn.disabled = !this.value;
        });
        
        document.getElementById('confirmInviteStudent').addEventListener('click', function() {
            if (studentToInvite) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url("admin/course/student/invite") ?>';
                
                const courseIdInput = document.createElement('input');
                courseIdInput.type = 'hidden';
                courseIdInput.name = 'course_id';
                courseIdInput.value = '<?= $course['id'] ?>';
                
                const studentIdInput = document.createElement('input');
                studentIdInput.type = 'hidden';
                studentIdInput.name = 'student_id';
                studentIdInput.value = studentToInvite;
                
                form.appendChild(courseIdInput);
                form.appendChild(studentIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Remove student functionality
        let studentToRemove = null;
        
        function removeStudent(studentId, studentName) {
            studentToRemove = studentId;
            document.getElementById('studentNameToRemove').textContent = studentName;
            
            const removeModal = new bootstrap.Modal(document.getElementById('removeStudentModal'));
            removeModal.show();
        }
        
        document.getElementById('confirmRemoveStudent').addEventListener('click', function() {
            if (studentToRemove) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url("admin/course/student/remove") ?>';
                
                const courseIdInput = document.createElement('input');
                courseIdInput.type = 'hidden';
                courseIdInput.name = 'course_id';
                courseIdInput.value = '<?= $course['id'] ?>';
                
                const studentIdInput = document.createElement('input');
                studentIdInput.type = 'hidden';
                studentIdInput.name = 'student_id';
                studentIdInput.value = studentToRemove;
                
                form.appendChild(courseIdInput);
                form.appendChild(studentIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Add Section functionality - Redirect to course management with pre-filled data
        function addSection(course) {
            // Store course data in sessionStorage for the course management page
            sessionStorage.setItem('addSectionData', JSON.stringify(course));
            
            // Redirect to course management page with a flag to open the add modal
            window.location.href = '<?= base_url("admin/courses") ?>?addSection=true';
        }
    </script>
</body>
</html>