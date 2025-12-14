<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Course Materials' ?></title>
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
        
        .page-header h2::before {
            content: "📚";
            margin-right: 8px;
        }
        
        .material-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .material-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .material-card .card-header {
            background: white;
            border-bottom: 2px solid #000;
            padding: 1rem 1.5rem;
        }
        
        .material-card .card-header h5 {
            color: #333;
            font-weight: 600;
            margin: 0;
        }
        
        .download-btn {
            background-color: #333;
            border-color: #333;
            color: white;
            font-size: 0.85rem;
        }
        
        .download-btn:hover {
            background-color: #555;
            border-color: #555;
            color: white;
        }
        
        .no-materials-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
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
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <span class="navbar-brand" style="cursor: default;">
                🎓 LMS
            </span>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/courses') ?>">My Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('student/materials') ?>">Materials</a>
                    </li>
                </ul>
                
                <!-- Notification Bell Icon -->
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
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
        <div class="page-header">
            <h2>Course Materials</h2>
            <p class="text-muted mb-0">Download materials from your enrolled courses</p>
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

        <!-- Materials List -->
        <div class="row">
            <div class="col-12">
                <?php if (empty($materials)): ?>
                    <div class="no-materials-card">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-file-earmark-x text-muted" viewBox="0 0 16 16">
                                    <path d="M6.854 7.146a.5.5 0 1 0-.708.708L7.293 9l-1.147 1.146a.5.5 0 0 0 .708.708L8 9.707l1.146 1.147a.5.5 0 0 0 .708-.708L8.707 9l1.147-1.146a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146z"/>
                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                                </svg>
                            </div>
                            <h4 class="text-muted">No Materials Available</h4>
                            <p class="text-muted mb-4">Your instructors haven't uploaded any materials yet.</p>
                            <a href="<?= base_url('student/dashboard') ?>" class="btn btn-outline-dark">
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php 
                    // Group materials by course and then by period
                    $materialsByCourse = [];
                    foreach ($materials as $material) {
                        $period = $material['period'] ?? 'General';
                        $materialsByCourse[$material['course_title']][$period][] = $material;
                    }
                    ?>

                    <?php foreach ($materialsByCourse as $courseTitle => $courseMaterials): ?>
                        <div class="material-card">
                            <div class="card-header">
                                <h5>
                                    <i class="bi bi-book me-2"></i>
                                    <?= esc($courseTitle) ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Period Tabs -->
                                <?php if (count($courseMaterials) > 1): ?>
                                    <ul class="nav nav-tabs mb-3" id="periodTabs-<?= md5($courseTitle) ?>" role="tablist">
                                        <?php 
                                        $periods = ['Prelim', 'Midterm', 'Final', 'General'];
                                        $activeSet = false;
                                        foreach ($periods as $period): 
                                            if (isset($courseMaterials[$period])): 
                                                $isActive = !$activeSet;
                                                $activeSet = true;
                                        ?>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link <?= $isActive ? 'active' : '' ?>" 
                                                        id="<?= $period ?>-tab-<?= md5($courseTitle) ?>" 
                                                        data-bs-toggle="tab" 
                                                        data-bs-target="#<?= $period ?>-<?= md5($courseTitle) ?>" 
                                                        type="button" role="tab">
                                                    <?php
                                                    switch($period) {
                                                        case 'Prelim': echo '📖 Prelim'; break;
                                                        case 'Midterm': echo '📚 Midterm'; break;
                                                        case 'Final': echo '🎓 Final'; break;
                                                        default: echo '📋 General'; break;
                                                    }
                                                    ?>
                                                    <span class="badge bg-secondary ms-1"><?= count($courseMaterials[$period]) ?></span>
                                                </button>
                                            </li>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </ul>
                                <?php endif; ?>

                                <!-- Period Content -->
                                <div class="tab-content" id="periodTabContent-<?= md5($courseTitle) ?>">
                                    <?php 
                                    $periods = ['Prelim', 'Midterm', 'Final', 'General'];
                                    $activeSet = false;
                                    foreach ($periods as $period): 
                                        if (isset($courseMaterials[$period])): 
                                            $isActive = !$activeSet;
                                            $activeSet = true;
                                    ?>
                                        <div class="tab-pane fade <?= $isActive ? 'show active' : '' ?>" 
                                             id="<?= $period ?>-<?= md5($courseTitle) ?>" 
                                             role="tabpanel">
                                            <div class="list-group list-group-flush">
                                                <?php foreach ($courseMaterials[$period] as $material): ?>
                                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <span class="me-2">
                                                                    <?php
                                                                    $extension = strtolower(pathinfo($material['file_name'], PATHINFO_EXTENSION));
                                                                    switch($extension) {
                                                                        case 'pdf': echo '📄'; break;
                                                                        case 'doc':
                                                                        case 'docx': echo '📝'; break;
                                                                        default: echo '📋'; break;
                                                                    }
                                                                    ?>
                                                                </span>
                                                                <div>
                                                                    <h6 class="mb-0"><?= esc($material['material_title'] ?? $material['file_name']) ?></h6>
                                                                    <?php if (!empty($material['material_title']) && $material['material_title'] !== $material['file_name']): ?>
                                                                        <small class="text-muted"><?= esc($material['file_name']) ?></small>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">
                                                                <i class="bi bi-calendar3 me-1"></i>
                                                                Uploaded: <?= date('M d, Y g:i A', strtotime($material['created_at'])) ?>
                                                            </small>
                                                        </div>
                                                        <a href="<?= base_url("materials/download/{$material['id']}") ?>" class="btn download-btn btn-sm ms-3">
                                                            <i class="bi bi-download me-1"></i>
                                                            Download
                                                        </a>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
</body>
</html>
