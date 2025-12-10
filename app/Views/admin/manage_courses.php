<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Course Management - Admin Panel' ?></title>
    <!-- Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .course-card {
            transition: transform 0.2s;
        }
        .course-card:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            font-size: 0.9em;
        }
        .status-active { background-color: #28a745 !important; }
        .status-inactive { background-color: #6c757d !important; }
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
                        <a class="nav-link text-white" href="<?= base_url('admin/users') ?>">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-white" href="<?= base_url('admin/courses') ?>">Courses</a>
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
        <!-- Page header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>Course Management</h2>
                <p class="text-muted">Manage all courses - create, edit, assign teachers, and delete courses</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    Add New Course
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="input-group">
                    <input type="text" id="courseSearch" class="form-control" placeholder="Search courses by title, instructor, or ID...">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
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

        <!-- Courses table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">All Courses (<?= count($courses) ?>)</h5>
                
                <?php if (empty($courses)): ?>
                    <div class="alert alert-info">
                        No courses found. Click "Add New Course" to create one.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Course Title</th>
                                    <th>Instructor</th>
                                    <th>Academic Year</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><strong><?= esc($course['id']) ?></strong></td>
                                        <td>
                                            <strong><?= esc($course['title']) ?></strong>
                                            <?php if (!empty($course['description'])): ?>
                                                <br><small class="text-muted"><?= esc(substr($course['description'], 0, 50)) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= esc($course['instructor_name'] ?? 'Unassigned') ?>
                                            <?php if (!empty($course['instructor_email'])): ?>
                                                <br><small class="text-muted"><?= esc($course['instructor_email']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($course['academic_year'])): ?>
                                                <?= esc($course['academic_year']) ?>
                                            <?php else: ?>
                                                <small class="text-muted">Not set</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($course['start_date']) && !empty($course['end_date'])): ?>
                                                <small><?= date('M d, Y', strtotime($course['start_date'])) ?></small><br>
                                                <small>to <?= date('M d, Y', strtotime($course['end_date'])) ?></small>
                                            <?php elseif (!empty($course['start_date'])): ?>
                                                <small>From <?= date('M d, Y', strtotime($course['start_date'])) ?></small>
                                            <?php else: ?>
                                                <small class="text-muted">Not set</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge status-<?= esc($course['status']) ?>">
                                                <?= ucfirst(esc($course['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url("admin/course/{$course['id']}/upload") ?>" class="btn btn-sm btn-success" title="Upload Materials">
                                                    Materials
                                                </a>
                                                <button class="btn btn-sm btn-primary" 
                                                        onclick="editCourse(<?= htmlspecialchars(json_encode($course), ENT_QUOTES, 'UTF-8') ?>)">
                                                    Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="deleteCourse(<?= $course['id'] ?>, '<?= esc($course['title']) ?>')">
                                                    Delete
                                                </button>
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

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add New Course</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('admin/courses/create') ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Course ID (4 digits) *</label>
                            <input type="text" class="form-control" name="course_id" required pattern="[0-9]{4}" maxlength="4" placeholder="e.g., 1001">
                            <small class="text-muted">Must be exactly 4 digits (e.g., 1001, 2050)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course Title *</label>
                            <input type="text" class="form-control" name="title" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" maxlength="1000"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assign Instructor *</label>
                            <select class="form-select" name="instructor_id" required>
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>"><?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($teachers)): ?>
                                <small class="text-danger">No teachers available. Please create teacher accounts first.</small>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Academic Year</label>
                            <select class="form-select" name="academic_year">
                                <option value="">-- Select Year --</option>
                                <?php 
                                $currentYear = (int)date('Y');
                                for ($year = $currentYear; $year <= 2099; $year++): 
                                ?>
                                    <option value="<?= $year ?>"><?= $year ?> - <?= $year + 1 ?></option>
                                <?php endfor; ?>
                            </select>
                            <small class="text-muted">Select the starting year of the academic year</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Create Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div class="modal fade" id="editCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Course</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('admin/courses/update') ?>" method="POST">
                    <input type="hidden" name="course_id" id="edit_course_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Course ID</label>
                            <input type="text" class="form-control" id="edit_course_id_display" disabled>
                            <small class="text-muted">Course ID cannot be changed</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course Title *</label>
                            <input type="text" class="form-control" name="title" id="edit_title" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3" maxlength="1000"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assign Instructor *</label>
                            <select class="form-select" name="instructor_id" id="edit_instructor_id" required>
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>"><?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="edit_start_date" min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" id="edit_end_date" min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Academic Year</label>
                            <select class="form-select" name="academic_year" id="edit_academic_year">
                                <option value="">-- Select Year --</option>
                                <?php 
                                $currentYear = (int)date('Y');
                                for ($year = $currentYear; $year <= 2099; $year++): 
                                ?>
                                    <option value="<?= $year ?>"><?= $year ?> - <?= $year + 1 ?></option>
                                <?php endfor; ?>
                            </select>
                            <small class="text-muted">Select the starting year of the academic year</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Course Modal -->
    <div class="modal fade" id="deleteCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('admin/courses/delete') ?>" method="POST">
                    <input type="hidden" name="course_id" id="delete_course_id">
                    <div class="modal-body">
                        <p>Are you sure you want to delete this course?</p>
                        <p class="text-danger"><strong id="delete_course_title"></strong></p>
                        <p class="text-muted">This action cannot be undone. Courses with enrolled students cannot be deleted.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Search functionality for courses table
        document.getElementById('courseSearch').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Edit course function
        function editCourse(course) {
            document.getElementById('edit_course_id').value = course.id;
            document.getElementById('edit_course_id_display').value = course.id;
            document.getElementById('edit_title').value = course.title;
            document.getElementById('edit_description').value = course.description || '';
            document.getElementById('edit_instructor_id').value = course.instructor_id;
            document.getElementById('edit_start_date').value = course.start_date || '';
            document.getElementById('edit_end_date').value = course.end_date || '';
            document.getElementById('edit_academic_year').value = course.academic_year || '';
            document.getElementById('edit_status').value = course.status;
            
            var editModal = new bootstrap.Modal(document.getElementById('editCourseModal'));
            editModal.show();
        }

        // Delete course function
        function deleteCourse(courseId, courseTitle) {
            document.getElementById('delete_course_id').value = courseId;
            document.getElementById('delete_course_title').textContent = courseTitle;
            
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteCourseModal'));
            deleteModal.show();
        }
    </script>
    
    <!-- jQuery and Notification Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
</body>
</html>
