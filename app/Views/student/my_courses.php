<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Courses - LMS' ?></title>
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
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .page-header h2 {
            color: #333;
            margin-bottom: 0.25rem;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .page-header h2::before {
            content: "📚";
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
            margin-bottom: 1.5rem;
        }
        
        .courses-section {
            background: white;
            border: 2px solid #000;
            border-radius: 6px;
            padding: 1rem;
        }
        
        .courses-section h5 {
            color: #333;
            margin-bottom: 0.75rem;
            font-weight: 600;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        .course-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .course-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .no-courses-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 2rem;
            text-align: center;
        }
        
        .no-courses-card h4 {
            color: #333;
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
        }
        
        .no-courses-card p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .alert {
            border-radius: 8px;
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .dropdown-menu {
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <span class="navbar-brand" style="cursor: default;">LMS</span>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/dashboard') ?>">Dashboard</a>
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
        <!-- Page Header -->
        <div class="page-header">
            <h2>My Courses</h2>
            <p>View and access all your enrolled courses</p>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="input-group">
                        <input type="text" id="courseSearch" class="form-control" placeholder="Search courses by title or instructor...">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                    </div>
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

        <!-- Enrolled Courses -->
        <div class="courses-section">
            <h5>Enrolled Courses (<?= count($enrolledCourses) ?>)</h5>
            
            <?php if (empty($enrolledCourses)): ?>
                <div class="no-courses-card">
                    <div class="mb-3">
                        <i class="bi bi-book" style="font-size: 2.5rem; color: #666;"></i>
                    </div>
                    <h4>No Courses Enrolled</h4>
                    <p class="mb-3">You haven't enrolled in any courses yet.</p>
                    <a href="<?= base_url('courses') ?>" class="btn btn-primary btn-sm">
                        Browse Available Courses
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($enrolledCourses as $course): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card course-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                    <p class="card-text text-muted"><?= esc($course['description']) ?></p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <strong>Instructor:</strong> <?= esc($course['instructor_name']) ?><br>
                                            <strong>Enrolled:</strong> <?php 
                                            $enrollmentDate = $course['enrollment_date'] ?? null;
                                            if ($enrollmentDate && $enrollmentDate !== '0000-00-00 00:00:00' && strtotime($enrollmentDate) > 0) {
                                                echo date('M j, Y', strtotime($enrollmentDate));
                                            } else {
                                                echo 'Recently';
                                            }
                                            ?>
                                        </small>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-success">Enrolled</span>
                                        <a href="<?= base_url('student/course/' . $course['course_id']) ?>" 
                                           class="btn btn-primary btn-sm">
                                            View Course
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>