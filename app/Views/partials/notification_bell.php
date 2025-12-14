<!-- Simple Notification Bell -->
<div class="dropdown">
    <button class="btn btn-link position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
        <i class="bi bi-bell fs-5 text-dark"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="display: none;">
            0
        </span>
    </button>
    
    <div class="dropdown-menu dropdown-menu-end" style="width: 300px; max-height: 400px; overflow-y: auto;">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Notifications</h6>
            <button class="btn btn-sm btn-outline-primary" id="markAllRead" style="display: none;">
                Mark all read
            </button>
        </div>
        <div class="dropdown-divider"></div>
        <div id="notificationList">
            <div class="text-center py-3 text-muted">
                <p class="mb-0">No notifications</p>
            </div>
        </div>
    </div>
</div>

<!-- Simple Notification Styles -->
<style>
.notification-item {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
}

.notification-item.unread {
    background-color: #f0f8ff;
}

.notification-message {
    font-size: 0.9rem;
    margin-bottom: 4px;
}

.notification-time {
    font-size: 0.8rem;
    color: #666;
}
</style>