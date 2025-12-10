<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Courses - LMS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .course-card {
            transition: transform 0.2s;
        }
        .course-card:hover {
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
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #0d6efd;">
        <div class="container">
            <a class="navbar-brand fw-bold text-white" href="<?= base_url('student/dashboard') ?>">
                ITE311 FUNDAR LMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= base_url('student/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-white" href="<?= base_url('student/courses') ?>">My Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= base_url('student/materials') ?>">Materials</a>
                    </li>
                </ul>
                
                <!-- Notification Bell Icon -->
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative text-white" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= esc($user['name']) ?>
                            <span class="badge bg-dark ms-2">STUDENT</span>
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
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card text-white" style="background-color: #0d6efd;">
                    <div class="card-body">
                        <h2 class="mb-0">📚 My Courses</h2>
                        <p class="mb-0">View and access all your enrolled courses</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="input-group">
                    <input type="text" id="courseSearch" class="form-control" placeholder="Search courses by title or instructor...">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Enrolled Courses (<?= count($enrolledCourses) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($enrolledCourses)): ?>
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-book-open fa-4x text-muted"></i>
                                </div>
                                <h4 class="text-muted">No Courses Enrolled</h4>
                                <p class="text-muted mb-4">You haven't enrolled in any courses yet.</p>
                                <a href="<?= base_url('student/dashboard') ?>" class="btn btn-primary">
                                    Browse Available Courses
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($enrolledCourses as $course): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                                <p class="card-text text-muted"><?= esc($course['description']) ?></p>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <strong>Instructor:</strong> <?= esc($course['instructor_name']) ?><br>
                                                        <strong>Enrolled:</strong> <?= date('M j, Y', strtotime($course['enrollment_date'])) ?>
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
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
</body>
</html>