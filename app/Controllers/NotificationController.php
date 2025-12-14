<?php

namespace App\Controllers;

use App\Models\NotificationModel;

/**
 * NotificationController - Handles notification operations
 */
class NotificationController extends BaseController
{
    protected $session;
    protected $notificationModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Get notifications for the current user (AJAX endpoint)
     */
    public function index()
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.'
            ])->setStatusCode(401);
        }

        $userId = $this->session->get('userID');

        try {
            // Get unread count
            $unreadCount = $this->notificationModel->getUnreadCount($userId);
            
            // Get recent notifications (limit to 10)
            $notifications = $this->notificationModel->getNotificationsForUser($userId, 10);
            
            // Format notifications with time ago
            foreach ($notifications as &$notification) {
                $notification['time_ago'] = $this->timeAgo($notification['created_at']);
            }

            return $this->response->setJSON([
                'success' => true,
                'unread_count' => $unreadCount,
                'notifications' => $notifications
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Notification fetch error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch notifications.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Mark a specific notification as read
     */
    public function markRead($notificationId)
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.'
            ])->setStatusCode(401);
        }

        $userId = $this->session->get('userID');

        try {
            // Verify the notification belongs to the current user
            $notification = $this->notificationModel->find($notificationId);
            if (!$notification || $notification['user_id'] != $userId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Notification not found or access denied.'
                ])->setStatusCode(404);
            }

            // Mark as read
            $success = $this->notificationModel->markAsRead($notificationId);
            
            if ($success) {
                // Get updated unread count
                $unreadCount = $this->notificationModel->getUnreadCount($userId);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Notification marked as read.',
                    'unread_count' => $unreadCount
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to mark notification as read.'
                ])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Mark notification read error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Mark all notifications as read for the current user
     */
    public function markAllRead()
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.'
            ])->setStatusCode(401);
        }

        $userId = $this->session->get('userID');

        try {
            $success = $this->notificationModel->markAllAsRead($userId);
            
            if ($success) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'All notifications marked as read.',
                    'unread_count' => 0
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to mark all notifications as read.'
                ])->setStatusCode(500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Mark all notifications read error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Helper method: Check if user is logged in
     */
    private function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') === true;
    }

    /**
     * Helper method: Convert timestamp to "time ago" format
     */
    private function timeAgo($datetime): string
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) {
            return 'Just now';
        } elseif ($time < 3600) {
            $minutes = floor($time / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($time < 86400) {
            $hours = floor($time / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($time < 2592000) {
            $days = floor($time / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', strtotime($datetime));
        }
    }
}