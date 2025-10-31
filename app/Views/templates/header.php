<?php
/**
 * Dynamic Navigation Header Template
 * Displays different navigation items based on user role
 */

// Get session service
$session = \Config\Services::session();
$isLoggedIn = $session->get('isLoggedIn') === true;
$userRole = $session->get('role');
$userName = $session->get('name');
?>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="<?= base_url() ?>">
            ITE311 FUNDAR LMS
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if ($isLoggedIn): ?>
                <!-- Role-based navigation menu -->
                <ul class="navbar-nav me-auto">
                    <?php if ($userRole === 'admin'): ?>
                        <!-- Admin-specific navigation -->
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() == 'admin/dashboard' ? 'active' : '' ?>" 
                               href="<?= base_url('admin/dashboard') ?>">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'admin/users') !== false ? 'active' : '' ?>" 
                               href="<?= base_url('admin/users') ?>">
                                User Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'admin/courses') !== false ? 'active' : '' ?>" 
                               href="<?= base_url('admin/courses') ?>">
                                Course Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('admin/reports') ?>">
                                Reports
                            </a>
                        </li>
                    <?php elseif ($userRole === 'teacher'): ?>
                        <!-- Teacher-specific navigation -->
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() == 'teacher/dashboard' ? 'active' : '' ?>" 
                               href="<?= base_url('teacher/dashboard') ?>">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'teacher/courses') !== false ? 'active' : '' ?>" 
                               href="<?= base_url('teacher/courses') ?>">
                                My Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'teacher/assignments') !== false ? 'active' : '' ?>" 
                               href="<?= base_url('teacher/assignments') ?>">
                                Assignments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('teacher/students') ?>">
                                Students
                            </a>
                        </li>
                    <?php elseif ($userRole === 'student'): ?>
                        <!-- Student-specific navigation -->
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() == 'student/dashboard' ? 'active' : '' ?>" 
                               href="<?= base_url('student/dashboard') ?>">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'student/courses') !== false ? 'active' : '' ?>" 
                               href="<?= base_url('student/courses') ?>">
                                My Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'student/assignments') !== false ? 'active' : '' ?>" 
                               href="<?= base_url('student/assignments') ?>">
                                Assignments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('student/grades') ?>">
                                Grades
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Notifications dropdown -->
                <ul class="navbar-nav me-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span id="notificationBadge" class="badge bg-danger position-absolute top-0 start-100 translate-middle" style="display: none;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 500px; overflow-y: auto;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Notifications</span>
                                <button class="btn btn-sm btn-link text-decoration-none" id="markAllRead" style="display: none;">Mark all as read</button>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div id="notificationList" class="notification-list">
                                <div class="text-center py-3 text-muted">
                                    <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                    <p class="mb-0">No notifications</p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                
                <!-- User dropdown menu -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= esc($userName) ?>
                            <span class="<?= $userRole ?>-badge ms-2"><?= strtoupper($userRole) ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('profile') ?>">
                                Profile
                            </a></li>
                            <li><a class="dropdown-item" href="<?= base_url('settings') ?>">
                                Settings
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>">
                                Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            <?php else: ?>
                <!-- Guest navigation -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/about') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/contact') ?>">Contact</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('login') ?>">
                            Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('register') ?>">
                            Register
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
.navbar-custom {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}

.admin-badge {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
}

.teacher-badge {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
}

.student-badge {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
}

.nav-link.active {
    color: #0d6efd !important;
    font-weight: 500;
}

/* Notification styles */
.notification-dropdown {
    padding: 0;
}

.notification-dropdown .dropdown-header {
    padding: 12px 16px;
    background-color: #f8f9fa;
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

.notification-actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}

.notification-actions .btn {
    font-size: 0.75rem;
    padding: 4px 12px;
}

#notificationBadge {
    font-size: 0.65rem;
    padding: 0.25em 0.5em;
    border-radius: 10px;
}

.notification-list:empty::after {
    content: "No notifications";
    display: block;
    text-align: center;
    padding: 20px;
    color: #6c757d;
}
</style>