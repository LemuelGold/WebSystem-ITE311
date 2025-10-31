<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Notifications extends BaseController
{
    protected $notificationModel;
    protected $session;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Get notifications for the current user (AJAX endpoint)
     * Returns JSON with unread count and list of notifications
     */
    public function get(): ResponseInterface
    {
        // Check if user is logged in
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $userId = $this->session->get('userID');

        // Get unread count
        $unreadCount = $this->notificationModel->getUnreadCount($userId);

        // Get latest notifications (limit to 10)
        $notifications = $this->notificationModel->getNotificationsForUser($userId, 10);

        // Format notifications for display
        foreach ($notifications as &$notification) {
            // Calculate time ago
            $notification['time_ago'] = $this->timeAgo($notification['created_at']);
        }

        return $this->response->setJSON([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a notification as read (AJAX endpoint)
     * 
     * @param int $id The notification ID to mark as read
     */
    public function mark_as_read($id = null): ResponseInterface
    {
        // Check if user is logged in
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification ID is required'
            ])->setStatusCode(400);
        }

        $userId = (int)$this->session->get('userID');

        // Verify the notification belongs to this user
        $notification = $this->notificationModel->find($id);
        
        if (!$notification || !is_array($notification)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found'
            ])->setStatusCode(404);
        }

        if ((int)$notification['user_id'] !== $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized to modify this notification'
            ])->setStatusCode(403);
        }

        // Mark as read
        $result = $this->notificationModel->markAsRead($id);

        if ($result) {
            // Get updated unread count
            $unreadCount = $this->notificationModel->getUnreadCount($userId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read',
                'unread_count' => $unreadCount
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ])->setStatusCode(500);
        }
    }

    /**
     * Mark all notifications as read for the current user
     */
    public function mark_all_as_read(): ResponseInterface
    {
        // Check if user is logged in
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $userId = $this->session->get('userID');

        $result = $this->notificationModel->markAllAsRead($userId);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All notifications marked as read',
                'unread_count' => 0
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark all notifications as read'
            ])->setStatusCode(500);
        }
    }

    /**
     * Helper function to convert datetime to "time ago" format
     * 
     * @param string $datetime The datetime string
     * @return string Formatted time ago string
     */
    private function timeAgo($datetime): string
    {
        $timestamp = strtotime($datetime);
        $current = time();
        $difference = $current - $timestamp;

        if ($difference < 60) {
            return 'Just now';
        } elseif ($difference < 3600) {
            $minutes = floor($difference / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($difference < 86400) {
            $hours = floor($difference / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($difference < 604800) {
            $days = floor($difference / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $timestamp);
        }
    }
}
