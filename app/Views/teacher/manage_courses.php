<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Courses - Teacher Panel' ?></title>
    <!-- Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Force browser refresh -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
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
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .page-header h2::before {
            content: "📚";
            margin-right: 8px;
        }
        
        .page-header p {
            color: #666;
            margin-bottom: 0;
        }
        
        .search-container {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        
        .course-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        
        .course-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .no-courses-card {
            background: white;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 3rem;
            text-align: center;
        }
        
        .no-courses-card h4 {
            color: #333;
            margin-bottom: 1rem;
        }
        
        .no-courses-card p {
            color: #666;
        }
        
        .summary-card {
            background: white;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .summary-card h5 {
            color: #333;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .summary-card h5::before {
            content: "📊";
            margin-right: 8px;
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }
        
        .dropdown-menu {
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .alert {
            border-radius: 8px;
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
                        <a class="nav-link" href="<?= base_url('teacher/dashboard') ?>">Dashboard</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <?= view('partials/notification_bell') ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('logout') ?>">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page header -->
        <div class="page-header">
            <h2>My Courses</h2>
            <p>Manage your courses and enrolled students</p>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="input-group">
                        <input type="text" id="courseSearch" class="form-control" placeholder="Search courses by title...">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
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

        <!-- Courses List -->
        <?php if (empty($courses)): ?>
            <div class="no-courses-card">
                <h4>No Courses Assigned</h4>
                <p>You don't have any courses assigned yet. Please contact the administrator.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card course-card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?= esc($course['title']) ?>
                                    <?php if (!empty($course['section'])): ?>
                                        <span class="badge bg-primary ms-2">Section <?= esc($course['section']) ?></span>
                                    <?php endif; ?>
                                </h5>
                                <p class="card-text text-muted">
                                    <?= esc($course['description'] ?? 'No description available') ?>
                                </p>
                                
                                <?php if (!empty($course['schedule_time']) || !empty($course['room'])): ?>
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <?php if (!empty($course['schedule_time'])): ?>
                                                <i class="bi bi-clock"></i> <?= esc($course['schedule_time']) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($course['room'])): ?>
                                                <i class="bi bi-geo-alt ms-2"></i> <?= esc($course['room']) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <span class="badge bg-<?= $course['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst(esc($course['status'])) ?>
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Created: <?php 
                                        $createdDate = $course['created_at'] ?? null;
                                        if ($createdDate && $createdDate !== '0000-00-00 00:00:00' && strtotime($createdDate) > 0) {
                                            echo date('M d, Y', strtotime($createdDate));
                                        } else {
                                            echo 'Recently';
                                        }
                                        ?>
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="<?= base_url("teacher/course/{$course['id']}/students") ?>" 
                                       class="btn btn-primary">
                                        Manage Students
                                    </a>
                                    <a href="<?= base_url("teacher/course/{$course['id']}/upload") ?>" 
                                       class="btn btn-success">
                                        Upload Materials
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary Card -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="summary-card">
                        <h5>Course Summary</h5>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h3 class="text-primary"><?= count($courses) ?></h3>
                                <p class="mb-0">Total Courses</p>
                            </div>
                            <div class="col-md-4">
                                <h3 class="text-success">
                                    <?= count(array_filter($courses, function($c) { return $c['status'] === 'active'; })) ?>
                                </h3>
                                <p class="mb-0">Active Courses</p>
                            </div>
                            <div class="col-md-4">
                                <h3 class="text-warning">
                                    <?= count(array_filter($courses, function($c) { return $c['status'] === 'inactive'; })) ?>
                                </h3>
                                <p class="mb-0">Inactive Courses</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>

    
    <!-- Search Script -->
    <script>
        document.getElementById('courseSearch').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const courseCards = document.querySelectorAll('.col-md-6.mb-4');
            
            courseCards.forEach(function(card) {
                const text = card.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
