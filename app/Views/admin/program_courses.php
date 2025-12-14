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
        
        .year-section {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .year-header {
            background: #007bff;
            color: white;
            padding: 1rem;
            font-weight: 600;
        }
        
        .semester-section {
            border-bottom: 1px solid #eee;
        }
        
        .semester-header {
            background: #f8f9fa;
            padding: 0.75rem 1rem;
            font-weight: 500;
            border-bottom: 1px solid #ddd;
        }
        
        .course-item {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }
        
        .course-item:hover {
            background-color: #f8f9fa;
        }
        
        .course-item:last-child {
            border-bottom: none;
        }
        
        .course-code {
            background: #28a745;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .units-badge {
            background: #6c757d;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .required-badge {
            background: #dc3545;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .elective-badge {
            background: #ffc107;
            color: #000;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .prerequisite-info {
            background: #e3f2fd;
            border-left: 3px solid #2196f3;
            padding: 0.5rem;
            margin-top: 0.5rem;
            border-radius: 0 4px 4px 0;
            font-size: 0.85rem;
        }
        
        .empty-semester {
            padding: 2rem;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
        
        .available-courses-section {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .course-option {
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .course-option:hover {
            background-color: #f8f9fa;
        }
        
        .course-option:last-child {
            border-bottom: none;
        }
        
        .table-sm td {
            padding: 0.5rem;
            vertical-align: middle;
        }
        
        .available-course-row:hover {
            background-color: #f8f9fa;
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
                        <i class="bi bi-book"></i> <?= esc($program['program_name']) ?> Curriculum
                    </h4>
                    <p class="text-muted mb-0">
                        <span class="badge bg-primary me-2"><?= esc($program['program_code']) ?></span>
                        <?= $program['duration_years'] ?> Years Program
                        <?php if ($program['total_units']): ?>
                            • <?= $program['total_units'] ?> Total Units
                        <?php endif; ?>
                    </p>
                    <small class="text-info">
                        <i class="bi bi-info-circle"></i> 
                        Prerequisites ensure students complete foundational courses before advanced ones. 
                        Students must pass prerequisite courses to enroll in dependent courses.
                    </small>
                </div>
                <div>
                    <a href="<?= base_url('admin/programs') ?>" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back to Programs
                    </a>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                        <i class="bi bi-plus-circle"></i> Add Course
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

            <!-- Curriculum Display -->
            <?php if (empty($curriculum)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-book" style="font-size: 4rem; color: #ccc;"></i>
                    <h5 class="mt-3 mb-2">No Courses Assigned</h5>
                    <p class="text-muted">Start building the curriculum by adding courses to this program.</p>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                        <i class="bi bi-plus-circle"></i> Add First Course
                    </button>
                </div>
            <?php else: ?>
                <?php for ($year = 1; $year <= $program['duration_years']; $year++): ?>
                    <div class="year-section">
                        <div class="year-header">
                            <i class="bi bi-calendar-event"></i> Year <?= $year ?>
                        </div>
                        
                        <?php 
                        $semesters = ['1st Semester', '2nd Semester'];
                        foreach ($semesters as $semesterName): 
                        ?>
                            <div class="semester-section">
                                <div class="semester-header">
                                    <i class="bi bi-calendar3"></i> <?= $semesterName ?>
                                    <?php 
                                    $semesterCourses = $curriculum[$year][$semesterName] ?? [];
                                    $semesterUnits = array_sum(array_column($semesterCourses, 'units'));
                                    ?>
                                    <?php if (!empty($semesterCourses)): ?>
                                        <span class="badge bg-info ms-2"><?= count($semesterCourses) ?> Courses</span>
                                        <?php if ($semesterUnits > 0): ?>
                                            <span class="badge bg-secondary ms-1"><?= $semesterUnits ?> Units</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (empty($semesterCourses)): ?>
                                    <div class="empty-semester">
                                        <i class="bi bi-inbox"></i> No courses assigned for this semester
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($semesterCourses as $course): ?>
                                        <div class="course-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="course-code me-2"><?= esc($course['course_code']) ?></span>
                                                        <h6 class="mb-0 me-2"><?= esc($course['title']) ?></h6>
                                                        <?php if ($course['units']): ?>
                                                            <span class="units-badge me-2"><?= $course['units'] ?> Units</span>
                                                        <?php endif; ?>
                                                        <span class="<?= $course['is_required'] ? 'required-badge' : 'elective-badge' ?>">
                                                            <?= $course['is_required'] ? 'Required' : 'Elective' ?>
                                                        </span>
                                                    </div>
                                                    <?php if ($course['description']): ?>
                                                        <p class="text-muted mb-1 small"><?= esc($course['description']) ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($course['prerequisite_course_id']): ?>
                                                        <?php
                                                        // Find the prerequisite course details
                                                        $prerequisiteCourse = null;
                                                        foreach ($curriculum as $prereqYear => $prereqSemesters) {
                                                            foreach ($prereqSemesters as $prereqSemester => $prereqCourses) {
                                                                foreach ($prereqCourses as $prereqCourse) {
                                                                    if ($prereqCourse['course_id'] == $course['prerequisite_course_id']) {
                                                                        $prerequisiteCourse = $prereqCourse;
                                                                        break 3;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <div class="prerequisite-info">
                                                            <i class="bi bi-link-45deg text-primary"></i> 
                                                            <strong>Prerequisite:</strong> 
                                                            <?= $prerequisiteCourse ? esc($prerequisiteCourse['course_code']) . ' - ' . esc($prerequisiteCourse['title']) : 'Course not found' ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="removeCourse(<?= $course['id'] ?>, '<?= esc($course['title']) ?>')"
                                                            title="Remove from program">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endfor; ?>
            <?php endif; ?>

            <!-- Available Courses Section -->
            <?php if (!empty($availableCourses)): ?>
                <div class="available-courses-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="bi bi-plus-square"></i> Available Courses to Add
                            <span class="badge bg-secondary ms-2"><?= count($availableCourses) ?></span>
                        </h5>
                        <input type="text" class="form-control form-control-sm" id="available_courses_search" 
                               placeholder="Search available courses..." style="width: 250px;">
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Code</th>
                                    <th>Course Title</th>
                                    <th style="width: 80px;">Units</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="available_courses_list">
                                <?php foreach ($availableCourses as $course): ?>
                                    <tr class="available-course-row" data-search="<?= strtolower(esc($course['course_code']) . ' ' . esc($course['title'])) ?>">
                                        <td>
                                            <span class="badge bg-primary"><?= esc($course['course_code']) ?></span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= esc($course['title']) ?></strong>
                                                <?php if ($course['description']): ?>
                                                    <br><small class="text-muted"><?= esc(substr($course['description'], 0, 80)) ?><?= strlen($course['description']) > 80 ? '...' : '' ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($course['units']): ?>
                                                <span class="badge bg-secondary"><?= $course['units'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="addCourseToProgram(<?= $course['id'] ?>, '<?= esc($course['title']) ?>')"
                                                    title="Add to Program">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border: 2px solid #000; border-radius: 0;">
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333; font-weight: 600;">
                        <i class="bi bi-plus-circle text-success"></i> Add Course to Program
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= base_url('admin/program/' . $program['id'] . '/courses/add') ?>">
                    <?= csrf_field() ?>
                    <div class="modal-body" style="background: #f8f9fa;">
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Select Course *</label>
                            <div class="position-relative">
                                <input type="text" class="form-control" id="course_search" placeholder="Search courses..." autocomplete="off">
                                <select class="form-select" id="course_id" name="course_id" required style="display: none;">
                                    <option value="">Choose a course...</option>
                                    <?php foreach ($availableCourses as $course): ?>
                                        <option value="<?= $course['id'] ?>" data-units="<?= $course['units'] ?>" 
                                                data-search="<?= strtolower(esc($course['course_code']) . ' ' . esc($course['title'])) ?>">
                                            <?= esc($course['course_code']) ?> - <?= esc($course['title']) ?>
                                            <?= $course['units'] ? ' (' . $course['units'] . ' units)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="course_dropdown" class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto; display: none;">
                                    <?php foreach ($availableCourses as $course): ?>
                                        <a class="dropdown-item course-option" href="#" 
                                           data-value="<?= $course['id'] ?>" 
                                           data-units="<?= $course['units'] ?>"
                                           data-search="<?= strtolower(esc($course['course_code']) . ' ' . esc($course['title'])) ?>">
                                            <strong><?= esc($course['course_code']) ?></strong> - <?= esc($course['title']) ?>
                                            <?= $course['units'] ? '<span class="text-muted">(' . $course['units'] . ' units)</span>' : '' ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="year_level" class="form-label">Year Level *</label>
                                    <select class="form-select" id="year_level" name="year_level" required>
                                        <?php for ($i = 1; $i <= $program['duration_years']; $i++): ?>
                                            <option value="<?= $i ?>">Year <?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester *</label>
                                    <select class="form-select" id="semester" name="semester" required>
                                        <option value="1st Semester">1st Semester</option>
                                        <option value="2nd Semester">2nd Semester</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="prerequisite_course_id" class="form-label">Prerequisite Course</label>
                            <select class="form-select" id="prerequisite_course_id" name="prerequisite_course_id">
                                <option value="">No prerequisite</option>
                                <?php 
                                // Get all courses already in the program for prerequisites
                                $allProgramCourses = [];
                                foreach ($curriculum as $year => $semesters) {
                                    foreach ($semesters as $semester => $courses) {
                                        foreach ($courses as $course) {
                                            $allProgramCourses[] = $course;
                                        }
                                    }
                                }
                                ?>
                                <?php foreach ($allProgramCourses as $course): ?>
                                    <option value="<?= $course['course_id'] ?>" 
                                            data-year="<?= $course['year_level'] ?>" 
                                            data-semester="<?= $course['semester'] ?>">
                                        Year <?= $course['year_level'] ?> - <?= $course['semester'] ?> | 
                                        <?= esc($course['course_code']) ?> - <?= esc($course['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Select a course that must be completed before this one. Only courses from earlier years/semesters will be shown as valid prerequisites.</div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_required" name="is_required" checked>
                                <label class="form-check-label" for="is_required">
                                    Required Course
                                </label>
                                <div class="form-text">Uncheck if this is an elective course</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="background: white; border-top: 1px solid #ddd;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Course Modal -->
    <div class="modal fade" id="removeCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border: 2px solid #000; border-radius: 0;">
                <div class="modal-header" style="background: white; border-bottom: 2px solid #000;">
                    <h5 class="modal-title" style="color: #333; font-weight: 600;">
                        <i class="bi bi-exclamation-triangle text-danger"></i> Remove Course
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background: #f8f9fa;">
                    <p>Are you sure you want to remove <strong id="removeCourseTitle"></strong> from this program?</p>
                    <p class="text-danger mb-0">
                        <i class="bi bi-exclamation-triangle"></i> 
                        This will remove the course from the curriculum but won't delete the course itself.
                    </p>
                </div>
                <div class="modal-footer" style="background: white; border-top: 1px solid #ddd;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemoveCourse">Remove Course</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Add course to program function (from available courses section)
        function addCourseToProgram(courseId, courseTitle) {
            document.getElementById('course_id').value = courseId;
            var addModal = new bootstrap.Modal(document.getElementById('addCourseModal'));
            addModal.show();
        }

        // Remove course function
        let courseToRemove = null;
        
        function removeCourse(programCourseId, courseTitle) {
            courseToRemove = programCourseId;
            document.getElementById('removeCourseTitle').textContent = courseTitle;
            
            var removeModal = new bootstrap.Modal(document.getElementById('removeCourseModal'));
            removeModal.show();
        }
        
        document.getElementById('confirmRemoveCourse').addEventListener('click', function() {
            if (courseToRemove) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url("admin/program/courses/remove") ?>';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '<?= csrf_token() ?>';
                csrfInput.value = '<?= csrf_hash() ?>';
                
                const programCourseIdInput = document.createElement('input');
                programCourseIdInput.type = 'hidden';
                programCourseIdInput.name = 'program_course_id';
                programCourseIdInput.value = courseToRemove;
                
                form.appendChild(csrfInput);
                form.appendChild(programCourseIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Update prerequisite options based on selected year and semester
        function updatePrerequisiteOptions() {
            const yearLevel = parseInt(document.getElementById('year_level').value);
            const semester = document.getElementById('semester').value;
            const prerequisiteSelect = document.getElementById('prerequisite_course_id');
            
            // Get all prerequisite options
            const allOptions = prerequisiteSelect.querySelectorAll('option[data-year]');
            
            // Hide all options first
            allOptions.forEach(option => {
                option.style.display = 'none';
                option.disabled = true;
            });
            
            // Show only valid prerequisites (from earlier years/semesters)
            allOptions.forEach(option => {
                const optionYear = parseInt(option.getAttribute('data-year'));
                const optionSemester = option.getAttribute('data-semester');
                
                // Show if it's from an earlier year
                if (optionYear < yearLevel) {
                    option.style.display = 'block';
                    option.disabled = false;
                }
                // Or if it's from the same year but earlier semester
                else if (optionYear === yearLevel && semester === '2nd Semester' && optionSemester === '1st Semester') {
                    option.style.display = 'block';
                    option.disabled = false;
                }
            });
            
            // Reset selection if current selection is now invalid
            const currentValue = prerequisiteSelect.value;
            if (currentValue && prerequisiteSelect.querySelector(`option[value="${currentValue}"]`).disabled) {
                prerequisiteSelect.value = '';
            }
        }
        
        // Add event listeners
        document.getElementById('year_level').addEventListener('change', updatePrerequisiteOptions);
        document.getElementById('semester').addEventListener('change', updatePrerequisiteOptions);
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updatePrerequisiteOptions);

        // Course search functionality for modal dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('course_search');
            const dropdown = document.getElementById('course_dropdown');
            const hiddenSelect = document.getElementById('course_id');
            const courseOptions = document.querySelectorAll('.course-option');

            if (searchInput && dropdown) {
                searchInput.addEventListener('focus', function() {
                    dropdown.style.display = 'block';
                });

                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    let hasResults = false;

                    courseOptions.forEach(option => {
                        const searchData = option.getAttribute('data-search');
                        if (searchData.includes(searchTerm)) {
                            option.style.display = 'block';
                            hasResults = true;
                        } else {
                            option.style.display = 'none';
                        }
                    });

                    dropdown.style.display = hasResults ? 'block' : 'none';
                });

                courseOptions.forEach(option => {
                    option.addEventListener('click', function(e) {
                        e.preventDefault();
                        const value = this.getAttribute('data-value');
                        const text = this.textContent.trim();
                        
                        searchInput.value = text;
                        hiddenSelect.value = value;
                        dropdown.style.display = 'none';
                    });
                });

                // Hide dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.style.display = 'none';
                    }
                });
            }

            // Available courses search functionality
            const availableSearch = document.getElementById('available_courses_search');
            const availableRows = document.querySelectorAll('.available-course-row');

            if (availableSearch && availableRows.length > 0) {
                availableSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();

                    availableRows.forEach(row => {
                        const searchData = row.getAttribute('data-search');
                        if (searchData.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>