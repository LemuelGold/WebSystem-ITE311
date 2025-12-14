<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Course Search' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            border-radius: 6px;
            padding: 0.75rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .page-header h1 {
            color: #333;
            margin-bottom: 0.25rem;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .page-header h1::before {
            content: "🔍";
            margin-right: 6px;
        }
        
        .page-header p {
            color: #666;
            margin-bottom: 0;
            font-size: 0.9rem;
        }
        
        .search-container {
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .courses-grid {
            margin-bottom: 2rem;
        }
        
        .course-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .course-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #eee;
        }
        
        .course-title {
            color: #333;
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            flex: 1;
            margin-right: 1rem;
        }
        
        .course-status {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .course-content {
            flex: 1;
            margin-bottom: 1rem;
        }
        
        .course-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }
        
        .course-meta {
            margin-bottom: 1rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            color: #666;
        }
        
        .meta-item i {
            margin-right: 0.5rem;
            width: 16px;
            color: #999;
        }
        
        .course-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: auto;
        }
        
        .course-actions .btn {
            flex: 1;
            font-size: 0.85rem;
        }
        
        .no-courses-message {
            background: white;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 2rem;
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .btn-outline-primary {
            color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .btn-outline-primary:hover {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php 
                $session = \Config\Services::session();
                $isLoggedIn = $session->get('isLoggedIn') === true;
                $userRole = $session->get('role');
                ?>
                
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <?php if ($isLoggedIn): ?>
                            <?php 
                            $dashboardUrl = base_url('dashboard');
                            switch($userRole) {
                                case 'admin':
                                    $dashboardUrl = base_url('admin/dashboard');
                                    break;
                                case 'teacher':
                                    $dashboardUrl = base_url('teacher/dashboard');
                                    break;
                                case 'student':
                                    $dashboardUrl = base_url('student/dashboard');
                                    break;
                            }
                            ?>
                            <a class="nav-link" href="<?= $dashboardUrl ?>">Dashboard</a>
                        <?php else: ?>
                            <a class="nav-link" href="<?= base_url() ?>">Home</a>
                        <?php endif; ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('courses') ?>">Courses</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <!-- Notification Bell -->
                        <?= view('partials/notification_bell') ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('logout') ?>">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('login') ?>">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <!-- Page Header -->
        <div class="page-header">
            <?php if (isset($userRole) && $userRole === 'student' && isset($studentProgram) && $studentProgram): ?>
                <h1><?= esc($studentProgram['program_code']) ?> Program Courses</h1>
                <p>Browse courses from your <?= esc($studentProgram['program_name']) ?> curriculum</p>
                <div class="program-info mt-2">
                    <span class="badge bg-primary me-2">Year <?= $studentProgram['current_year_level'] ?></span>
                    <span class="badge bg-secondary me-2"><?= $studentProgram['current_semester'] ?></span>
                    <span class="badge bg-info"><?= $studentProgram['academic_year'] ?></span>
                </div>
            <?php else: ?>
                <h1>Course Search</h1>
                <p>Search and explore available courses</p>
            <?php endif; ?>
        </div>

        <!-- Search Form -->
        <div class="search-container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <form id="searchForm" class="d-flex">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" 
                                   placeholder="Search courses..." name="search_term">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Courses Container -->
        <div class="courses-grid">
            <?php if (empty($courses)): ?>
                <div class="no-courses-message">
                    <div class="text-center py-3">
                        <?php if (isset($userRole) && $userRole === 'student'): ?>
                            <?php if (isset($studentProgram) && $studentProgram): ?>
                                <i class="bi bi-mortarboard" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3 mb-2">No Courses Available</h4>
                                <p class="text-muted">
                                    No courses are currently available in your <strong><?= esc($studentProgram['program_code']) ?></strong> program curriculum.
                                    <br>Please check back later or contact your administrator.
                                </p>
                            <?php else: ?>
                                <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; color: #ffc107;"></i>
                                <h5 class="mt-2 mb-2">Program Enrollment Required</h5>
                                <p class="text-muted">
                                    You must be enrolled in an academic program to view available courses.
                                    <br>Please contact the administrator to enroll in a program (BSIT, BSCS, etc.).
                                </p>
                                <a href="<?= base_url('student/dashboard') ?>" class="btn btn-primary mt-3">
                                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <i class="bi bi-book" style="font-size: 3rem; color: #666;"></i>
                            <h4 class="mt-3">No Courses Available</h4>
                            <p class="text-muted">There are currently no courses available for enrollment.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Course</th>
                                <th>Instructor</th>
                                <th>Units</th>
                                <th>Term</th>
                                <th>Academic Year</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="coursesContainer">
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= esc($course['title']) ?></strong>
                                            <?php if (!empty($course['description'])): ?>
                                                <br><small class="text-muted"><?= esc(substr($course['description'], 0, 60)) ?>...</small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['instructor_name'])): ?>
                                            <?= esc($course['instructor_name']) ?>
                                        <?php else: ?>
                                            <small class="text-muted">Unassigned</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['units'])): ?>
                                            <span class="badge bg-primary"><?= esc($course['units']) ?></span>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['term'])): ?>
                                            <span class="badge bg-info"><?= esc($course['term']) ?></span>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['academic_year'])): ?>
                                            <small><?= esc($course['academic_year']) ?> - <?= esc($course['academic_year'] + 1) ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= ($course['status'] ?? 'active') === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= ucfirst($course['status'] ?? 'Active') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('courses/view/' . $course['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- No Results Message (hidden by default) -->
        <div id="noResults" class="col-12" style="display: none;">
            <div class="alert alert-info text-center">
                No courses found matching your search.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
    
    <script>
        $(document).ready(function() {
            // Client-side filtering
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('.course-card').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Server-side search with AJAX
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                var searchTerm = $('#searchInput').val();

                $.get('<?= base_url('/courses/search') ?>', {search_term: searchTerm}, function(data) {
                    $('#coursesContainer').empty();
                    
                    if (data.courses && data.courses.length > 0) {
                        $.each(data.courses, function(index, course) {
                            var courseHtml = `
                                <div class="col-md-4 mb-4">
                                    <div class="card course-card">
                                        <div class="card-body">
                                            <h5 class="card-title">${course.title}</h5>
                                            <p class="card-text">${course.description}</p>
                                            <a href="<?= base_url('courses/view/') ?>${course.id}" class="btn btn-primary">View Course</a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#coursesContainer').append(courseHtml);
                        });
                        $('#noResults').hide();
                    } else {
                        $('#coursesContainer').html('<div class="col-12"><div class="alert alert-info text-center">No courses found matching your search.</div></div>');
                    }
                });
            });
        });
    </script>
</body>
</html>
