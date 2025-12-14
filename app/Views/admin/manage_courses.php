<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Course Management - Admin Panel' ?></title>
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
                        <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/users') ?>">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('admin/courses') ?>">Courses</a>
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
        <!-- Page header with add course button -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card" style="border: 2px solid #000;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h2 class="mb-0 text-dark">Course Management</h2>
                            </div>
                            <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                                Add New Course
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Multiple sections of the same course are grouped together. To add sections, go to "View Students" → "Add Another Section".
                        </small>
                    </div>
                </div>
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
                                    <th>Units</th>
                                    <th>Term</th>
                                    <th>Instructor</th>
                                    <th>Sections</th>
                                    <th>Academic Year</th>
                                    <th>Total Enrolled</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><strong><?= esc($course['course_code'] ?? $course['id']) ?></strong></td>
                                        <td>
                                            <strong><?= esc($course['title']) ?></strong>
                                            <?php if (!empty($course['description'])): ?>
                                                <br><small class="text-muted"><?= esc(substr($course['description'], 0, 50)) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($course['units'])): ?>
                                                <span class="badge bg-primary"><?= esc($course['units']) ?> Unit<?= $course['units'] > 1 ? 's' : '' ?></span>
                                            <?php else: ?>
                                                <small class="text-muted">Not set</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($course['term'])): ?>
                                                <span class="badge bg-info text-dark"><?= esc($course['term']) ?></span>
                                            <?php else: ?>
                                                <small class="text-muted">Not set</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= esc($course['instructor_name'] ?? 'Unassigned') ?>
                                            <?php if (!empty($course['instructor_email'])): ?>
                                                <br><small class="text-muted"><?= esc($course['instructor_email']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <?= $course['section_count'] ?> Section<?= $course['section_count'] > 1 ? 's' : '' ?>
                                            </span>
                                            <br><small class="text-muted">Manage sections in "View Students"</small>
                                        </td>
                                        <td>
                                            <?php if (!empty($course['academic_year'])): ?>
                                                <strong><?= esc($course['academic_year']) ?> - <?= esc($course['academic_year'] + 1) ?></strong>
                                                <?php if (!empty($course['semester'])): ?>
                                                    <br><small class="text-primary"><?= esc($course['semester']) ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <small class="text-muted">Not set</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">
                                                <?= (int)($course['total_enrolled'] ?? 0) ?> Student<?= ($course['total_enrolled'] ?? 0) != 1 ? 's' : '' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php $status = $course['status'] ?? 'active'; ?>
                                            <span class="badge status-<?= esc($status) ?>">
                                                <?= ucfirst(esc($status)) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url("admin/course/{$course['id']}/students") ?>" class="btn btn-sm btn-info" title="View Students & Manage Sections">
                                                    <i class="bi bi-people"></i> Students
                                                </a>
                                                <a href="<?= base_url("admin/course/{$course['id']}/upload") ?>" class="btn btn-sm btn-danger" title="Upload Materials">
                                                    Materials
                                                </a>
                                                <button class="btn btn-sm btn-primary" 
                                                        onclick="editCourse(<?= htmlspecialchars(json_encode($course), ENT_QUOTES, 'UTF-8') ?>)"
                                                        title="Edit Course">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="deleteCourse(<?= $course['id'] ?>, '<?= esc($course['title']) ?>')"
                                                        title="Delete Course">
                                                    <i class="bi bi-trash"></i>
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
                            <small class="text-muted">Same course ID can be used for multiple sections with different schedules</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course Title *</label>
                            <input type="text" class="form-control" name="title" required maxlength="255" pattern="[a-zA-Z0-9\s\-\.]+" title="Only letters, numbers, spaces, hyphens, and periods are allowed">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" maxlength="1000"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Units</label>
                                <select class="form-select" name="units">
                                    <option value="">-- Select Units --</option>
                                    <option value="1">1 Unit</option>
                                    <option value="2">2 Units</option>
                                    <option value="3">3 Units</option>
                                    <option value="4">4 Units</option>
                                    <option value="5">5 Units</option>
                                    <option value="6">6 Units</option>
                                </select>
                                <small class="text-muted">Course credit units</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Term</label>
                                <select class="form-select" name="term">
                                    <option value="">-- Select Term --</option>
                                    <option value="Term 1">Term 1</option>
                                    <option value="Term 2">Term 2</option>
                                </select>
                                <small class="text-muted">Academic term</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Section</label>
                                <input type="text" class="form-control" name="section" maxlength="10" placeholder="e.g., A, B, 1, 2">
                                <small class="text-muted">Section identifier - helps distinguish multiple sections of same course</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Schedule</label>
                                <input type="text" class="form-control" name="schedule_time" maxlength="50" placeholder="e.g., MWF 9:00-10:00 AM">
                                <small class="text-muted">Class schedule time - prevents conflicts for same instructor</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Room</label>
                                <input type="text" class="form-control" name="room" maxlength="50" placeholder="e.g., Room 101, Lab A">
                                <small class="text-muted">Classroom assignment</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assign Instructor</label>
                            <select class="form-select" name="instructor_id">
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>"><?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($teachers)): ?>
                                <small class="text-danger">No teachers available. Please create teacher accounts first.</small>
                            <?php else: ?>
                                <small class="text-muted">Teachers can handle multiple sections of the same course with different schedules</small>
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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Academic Year <span class="text-danger" id="academic_year_required" style="display: none;">*</span></label>
                                <select class="form-select" name="academic_year">
                                    <option value="">-- Select Year --</option>
                                    <?php 
                                    $currentYear = (int)date('Y');
                                    for ($year = $currentYear; $year <= 2099; $year++): 
                                    ?>
                                        <option value="<?= $year ?>"><?= $year ?> - <?= $year + 1 ?></option>
                                    <?php endfor; ?>
                                </select>
                                <small class="text-muted">Select the starting year</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Semester</label>
                                <select class="form-select" name="semester">
                                    <option value="">-- Select Semester --</option>
                                    <option value="1st Semester">1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                </select>
                                <small class="text-muted">Select the semester (Academic Year required if selected)</small>
                            </div>
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
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333; font-weight: 600;">📝 Edit Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('admin/courses/update') ?>" method="POST">
                    <input type="hidden" name="course_id" id="edit_course_id">
                    <div class="modal-body" style="background: #f8f9fa;">
                        <!-- Course Information Section -->
                        <div class="mb-4 p-3" style="background: white; border: 1px solid #ddd; border-radius: 8px;">
                            <h6 class="mb-3" style="color: #333; font-weight: 600; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">📋 Course Information</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Change Course ID (Optional)</label>
                                <input type="number" class="form-control" name="new_course_id" id="edit_course_id_display" min="1" placeholder="Leave empty to keep current ID">
                                <small class="text-muted">Only fill this if you want to change the Course ID</small>
                                <small class="text-warning d-block"><i class="bi bi-exclamation-triangle"></i> Warning: Changing Course ID may affect enrollments and other data</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Course Title *</label>
                                <input type="text" class="form-control" name="title" id="edit_title" required maxlength="255" pattern="[a-zA-Z0-9\s\-\.]+" title="Only letters, numbers, spaces, hyphens, and periods are allowed">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" name="description" id="edit_description" rows="3" maxlength="1000" placeholder="Enter course description..."></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Units</label>
                                    <select class="form-select" name="units" id="edit_units">
                                        <option value="">-- Select Units --</option>
                                        <option value="1">1 Unit</option>
                                        <option value="2">2 Units</option>
                                        <option value="3">3 Units</option>
                                        <option value="4">4 Units</option>
                                        <option value="5">5 Units</option>
                                        <option value="6">6 Units</option>
                                    </select>
                                    <small class="text-muted">Course credit units</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Term</label>
                                    <select class="form-select" name="term" id="edit_term">
                                        <option value="">-- Select Term --</option>
                                        <option value="Term 1">Term 1</option>
                                        <option value="Term 2">Term 2</option>
                                    </select>
                                    <small class="text-muted">Academic term</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Section</label>
                                    <input type="text" class="form-control" name="section" id="edit_section" maxlength="10" placeholder="e.g., A, B, 1, 2">
                                    <small class="text-muted">Optional section identifier</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Schedule</label>
                                    <input type="text" class="form-control" name="schedule_time" id="edit_schedule_time" maxlength="50" placeholder="e.g., MWF 9:00-10:00 AM">
                                    <small class="text-muted">Class schedule time</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Room</label>
                                    <input type="text" class="form-control" name="room" id="edit_room" maxlength="50" placeholder="e.g., Room 101, Lab A">
                                    <small class="text-muted">Classroom assignment</small>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment & Schedule Section -->
                        <div class="mb-4 p-3" style="background: white; border: 1px solid #ddd; border-radius: 8px;">
                            <h6 class="mb-3" style="color: #333; font-weight: 600; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">👨‍🏫 Assignment & Schedule</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Assign Instructor</label>
                                <select class="form-select" name="instructor_id" id="edit_instructor_id">
                                    <option value="">-- Select Teacher --</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['id'] ?>"><?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" id="edit_start_date" min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="edit_end_date" min="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Academic Year <span class="text-danger" id="edit_academic_year_required" style="display: none;">*</span></label>
                                    <select class="form-select" name="academic_year" id="edit_academic_year">
                                        <option value="">-- Select Year --</option>
                                        <?php 
                                        $currentYear = (int)date('Y');
                                        for ($year = $currentYear; $year <= 2099; $year++): 
                                        ?>
                                            <option value="<?= $year ?>"><?= $year ?> - <?= $year + 1 ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <small class="text-muted">Select the starting year</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Semester</label>
                                    <select class="form-select" name="semester" id="edit_semester">
                                        <option value="">-- Select Semester --</option>
                                        <option value="1st Semester">1st Semester</option>
                                        <option value="2nd Semester">2nd Semester</option>
                                    </select>
                                    <small class="text-muted">Select the semester (Academic Year required if selected)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Status Section -->
                        <div class="p-3" style="background: white; border: 1px solid #ddd; border-radius: 8px;">
                            <h6 class="mb-3" style="color: #333; font-weight: 600; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">⚙️ Course Status</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status *</label>
                                <select class="form-select" name="status" id="edit_status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #ddd;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">Update Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Course Modal -->
    <div class="modal fade" id="deleteCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border: 2px solid #000; background-color: white;">
                <div class="modal-header" style="background-color: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title text-dark"><i class="bi bi-trash"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('admin/courses/delete') ?>" method="POST">
                    <input type="hidden" name="course_id" id="delete_course_id">
                    <div class="modal-body" style="background-color: white; padding: 1.5rem;">
                        <p class="text-dark mb-3">Are you sure you want to delete this course?</p>
                        <div class="alert alert-danger" style="border: 1px solid #dc3545; background-color: #f8d7da;">
                            <strong id="delete_course_title" class="text-danger"></strong>
                        </div>
                        <p class="text-muted small">This action cannot be undone. Courses with enrolled students cannot be deleted.</p>
                    </div>
                    <div class="modal-footer" style="background-color: white; border-top: 1px solid #ddd;">
                        <button type="button" class="btn" style="background-color: white; border: 2px solid #6c757d; color: #6c757d;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" style="background-color: white; border: 2px solid #dc3545; color: #dc3545;" onmouseover="this.style.backgroundColor='#dc3545'; this.style.color='white';" onmouseout="this.style.backgroundColor='white'; this.style.color='#dc3545';">Delete Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Test function
        function testFunction() {
            alert('JavaScript is working!');
        }

        // Global functions for button clicks
        window.editCourse = function(course) {
            console.log('Edit course called:', course);
            document.getElementById('edit_course_id').value = course.id;
            document.getElementById('edit_course_id_display').value = '';
            document.getElementById('edit_course_id_display').placeholder = 'Current ID: ' + (course.course_code || course.id) + ' (leave empty to keep current)';
            document.getElementById('edit_title').value = course.title;
            document.getElementById('edit_description').value = course.description || '';
            document.getElementById('edit_units').value = course.units || '';
            document.getElementById('edit_term').value = course.term || '';
            document.getElementById('edit_instructor_id').value = course.instructor_id;
            document.getElementById('edit_section').value = course.section || '';
            document.getElementById('edit_schedule_time').value = course.schedule_time || '';
            document.getElementById('edit_room').value = course.room || '';
            document.getElementById('edit_start_date').value = course.start_date || '';
            document.getElementById('edit_end_date').value = course.end_date || '';
            document.getElementById('edit_academic_year').value = course.academic_year || '';
            document.getElementById('edit_semester').value = course.semester || '';
            document.getElementById('edit_status').value = course.status || 'active';
            
            var editModal = new bootstrap.Modal(document.getElementById('editCourseModal'));
            editModal.show();
        };

        window.addSection = function(course) {
            console.log('Add section called:', course);
            const modal = new bootstrap.Modal(document.getElementById('addCourseModal'));
            
            document.querySelector('#addCourseModal input[name="course_id"]').value = course.course_code || course.id;
            document.querySelector('#addCourseModal input[name="title"]').value = course.title;
            document.querySelector('#addCourseModal textarea[name="description"]').value = course.description || '';
            
            if (course.units) {
                document.querySelector('#addCourseModal select[name="units"]').value = course.units;
            }
            if (course.term) {
                document.querySelector('#addCourseModal select[name="term"]').value = course.term;
            }
            if (course.instructor_id) {
                document.querySelector('#addCourseModal select[name="instructor_id"]').value = course.instructor_id;
            }
            if (course.academic_year) {
                document.querySelector('#addCourseModal select[name="academic_year"]').value = course.academic_year;
            }
            if (course.semester) {
                document.querySelector('#addCourseModal select[name="semester"]').value = course.semester;
            }
            
            document.querySelector('#addCourseModal input[name="section"]').value = '';
            document.querySelector('#addCourseModal input[name="schedule_time"]').value = '';
            document.querySelector('#addCourseModal input[name="room"]').value = '';
            
            // Add hidden field to indicate this is adding a section
            let addSectionFlag = document.querySelector('#addCourseModal input[name="is_adding_section"]');
            if (!addSectionFlag) {
                addSectionFlag = document.createElement('input');
                addSectionFlag.type = 'hidden';
                addSectionFlag.name = 'is_adding_section';
                document.querySelector('#addCourseModal form').appendChild(addSectionFlag);
            }
            addSectionFlag.value = '1';
            
            document.querySelector('#addCourseModal .modal-title').textContent = 'Add New Section - ' + course.title;
            
            const modalBody = document.querySelector('#addCourseModal .modal-body');
            let existingAlert = modalBody.querySelector('.section-alert');
            if (existingAlert) {
                existingAlert.remove();
            }
            
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-info section-alert';
            alertDiv.innerHTML = '<i class="bi bi-info-circle"></i> <strong>Adding new section:</strong> Course details are pre-filled. Just add section, schedule, and room information.';
            modalBody.insertBefore(alertDiv, modalBody.firstChild);
            
            modal.show();
            setTimeout(() => {
                document.querySelector('#addCourseModal input[name="section"]').focus();
            }, 500);
        };

        window.deleteCourse = function(courseId, courseTitle) {
            console.log('Delete course called:', courseId, courseTitle);
            document.getElementById('delete_course_id').value = courseId;
            document.getElementById('delete_course_title').textContent = courseTitle;
            
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteCourseModal'));
            deleteModal.show();
        };

        // Initialize Bootstrap tooltips and handle addSection redirect
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, JavaScript is working');
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Check if we're coming from an "Add Section" redirect
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('addSection') === 'true') {
                const courseData = sessionStorage.getItem('addSectionData');
                if (courseData) {
                    const course = JSON.parse(courseData);
                    // Clear the session storage
                    sessionStorage.removeItem('addSectionData');
                    // Auto-open the add section modal
                    setTimeout(() => {
                        window.addSection(course);
                    }, 500);
                }
            }
        });

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


        // Validation: Academic Year required when Semester is selected
        function validateSemesterAcademicYear(semesterField, academicYearField) {
            const semester = semesterField.value;
            const academicYear = academicYearField.value;
            
            if (semester && !academicYear) {
                alert('Academic Year is required when Semester is selected.');
                academicYearField.focus();
                return false;
            }
            return true;
        }

        // Add validation to Add Course form
        document.querySelector('#addCourseModal form').addEventListener('submit', function(e) {
            const semester = document.querySelector('#addCourseModal select[name="semester"]');
            const academicYear = document.querySelector('#addCourseModal select[name="academic_year"]');
            
            if (!validateSemesterAcademicYear(semester, academicYear)) {
                e.preventDefault();
            }
        });

        // Add validation to Edit Course form
        document.querySelector('#editCourseModal form').addEventListener('submit', function(e) {
            const semester = document.getElementById('edit_semester');
            const academicYear = document.getElementById('edit_academic_year');
            
            if (!validateSemesterAcademicYear(semester, academicYear)) {
                e.preventDefault();
            }
        });

        // Real-time validation feedback
        function setupRealTimeValidation(semesterField, academicYearField, requiredIndicator) {
            semesterField.addEventListener('change', function() {
                if (this.value) {
                    // Show required indicator for academic year
                    requiredIndicator.style.display = 'inline';
                    if (!academicYearField.value) {
                        academicYearField.style.borderColor = '#dc3545';
                        academicYearField.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
                    }
                } else {
                    // Hide required indicator
                    requiredIndicator.style.display = 'none';
                    academicYearField.style.borderColor = '';
                    academicYearField.style.boxShadow = '';
                }
            });
            
            academicYearField.addEventListener('change', function() {
                if (this.value) {
                    this.style.borderColor = '';
                    this.style.boxShadow = '';
                }
            });
        }

        // Setup real-time validation for both forms
        setupRealTimeValidation(
            document.querySelector('#addCourseModal select[name="semester"]'),
            document.querySelector('#addCourseModal select[name="academic_year"]'),
            document.getElementById('academic_year_required')
        );
        
        setupRealTimeValidation(
            document.getElementById('edit_semester'),
            document.getElementById('edit_academic_year'),
            document.getElementById('edit_academic_year_required')
        );



    // Reset modal when closed
    document.getElementById('addCourseModal').addEventListener('hidden.bs.modal', function () {
        // Reset modal title
        document.querySelector('#addCourseModal .modal-title').textContent = 'Add New Course';
        
        // Remove alert if exists
        const existingAlert = document.querySelector('#addCourseModal .section-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Remove the is_adding_section flag
        const addSectionFlag = document.querySelector('#addCourseModal input[name="is_adding_section"]');
        if (addSectionFlag) {
            addSectionFlag.remove();
        }
        
        // Reset form
        document.querySelector('#addCourseModal form').reset();
    });
    </script>
    
    <!-- jQuery and Notification Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
</body>
</html>
