/**
 * Notification System - jQuery Implementation
 * Handles real-time notification fetching and marking as read
 */

(function($) {
    'use strict';

    // Base URL for API calls
    const baseUrl = window.location.origin + '/ITE311-Fundar';
    
    // Notification manager object
    const NotificationManager = {
        // Fetch notifications from server
        fetchNotifications: function() {
            $.ajax({
                url: baseUrl + '/notifications',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        NotificationManager.updateBadge(response.unread_count);
                        NotificationManager.renderNotifications(response.notifications);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch notifications:', error);
                }
            });
        },

        // Update the notification badge count
        updateBadge: function(count) {
            const badge = $('#notificationBadge');
            const markAllBtn = $('#markAllRead');
            
            if (count > 0) {
                badge.text(count).show();
                markAllBtn.show();
            } else {
                badge.hide();
                markAllBtn.hide();
            }
        },

        // Render notifications in the dropdown
        renderNotifications: function(notifications) {
            const listContainer = $('#notificationList');
            
            if (!notifications || notifications.length === 0) {
                listContainer.html(`
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                        <p class="mb-0">No notifications</p>
                    </div>
                `);
                return;
            }

            let html = '';
            notifications.forEach(function(notification) {
                const unreadClass = notification.is_read == 0 ? 'unread' : '';
                const unreadIndicator = notification.is_read == 0 ? '<span class="badge bg-primary badge-sm">New</span>' : '';
                
                html += `
                    <div class="notification-item ${unreadClass}" data-id="${notification.id}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="notification-message">
                                    ${NotificationManager.escapeHtml(notification.message)}
                                </div>
                                <div class="notification-time">
                                    <i class="far fa-clock"></i> ${notification.time_ago}
                                </div>
                            </div>
                            ${unreadIndicator}
                        </div>
                        ${notification.is_read == 0 ? `
                            <div class="notification-actions">
                                <button class="btn btn-sm btn-outline-primary mark-read-btn" data-id="${notification.id}">
                                    <i class="fas fa-check"></i> Mark as read
                                </button>
                            </div>
                        ` : ''}
                    </div>
                `;
            });

            listContainer.html(html);

            // Attach click handlers to mark as read buttons
            $('.mark-read-btn').on('click', function(e) {
                e.stopPropagation();
                const notificationId = $(this).data('id');
                NotificationManager.markAsRead(notificationId);
            });
        },

        // Mark a notification as read
        markAsRead: function(notificationId) {
            $.ajax({
                url: baseUrl + '/notifications/mark_read/' + notificationId,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Remove the notification item from the list
                        $(`.notification-item[data-id="${notificationId}"]`).fadeOut(300, function() {
                            $(this).remove();
                            
                            // Update badge count
                            NotificationManager.updateBadge(response.unread_count);
                            
                            // If no notifications left, show empty state
                            if ($('.notification-item').length === 0) {
                                $('#notificationList').html(`
                                    <div class="text-center py-3 text-muted">
                                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                        <p class="mb-0">No notifications</p>
                                    </div>
                                `);
                            }
                        });
                    } else {
                        alert('Failed to mark notification as read: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to mark notification as read:', error);
                    alert('An error occurred while marking the notification as read.');
                }
            });
        },

        // Mark all notifications as read
        markAllAsRead: function() {
            $.ajax({
                url: baseUrl + '/notifications/mark_all_read',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Refresh notifications
                        NotificationManager.fetchNotifications();
                    } else {
                        alert('Failed to mark all notifications as read: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to mark all notifications as read:', error);
                    alert('An error occurred while marking all notifications as read.');
                }
            });
        },

        // Escape HTML to prevent XSS
        escapeHtml: function(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        },

        // Initialize the notification system
        init: function() {
            // Fetch notifications on page load
            this.fetchNotifications();

            // Set up auto-refresh every 60 seconds
            setInterval(function() {
                NotificationManager.fetchNotifications();
            }, 60000); // 60 seconds

            // Mark all as read button handler
            $('#markAllRead').on('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to mark all notifications as read?')) {
                    NotificationManager.markAllAsRead();
                }
            });

            // Refresh notifications when dropdown is opened
            $('#notificationDropdown').on('click', function() {
                NotificationManager.fetchNotifications();
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        NotificationManager.init();
    });

})(jQuery);
