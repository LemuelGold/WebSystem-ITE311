<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'message', 'is_read', 'created_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get the count of unread notifications for a specific user
     * 
     * @param int $userId The user ID to check notifications for
     * @return int The count of unread notifications
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    /**
     * Get the latest notifications for a specific user
     * 
     * @param int $userId The user ID to fetch notifications for
     * @param int $limit Maximum number of notifications to return (default: 5)
     * @return array Array of notification records
     */
    public function getNotificationsForUser(int $userId, int $limit = 5): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Mark a specific notification as read
     * 
     * @param int $notificationId The notification ID to mark as read
     * @return bool True if successful, false otherwise
     */
    public function markAsRead(int $notificationId): bool
    {
        return $this->update($notificationId, ['is_read' => 1]);
    }

    /**
     * Create a new notification
     * 
     * @param int $userId The user ID to send notification to
     * @param string $message The notification message
     * @return int|bool The insert ID if successful, false otherwise
     */
    public function createNotification(int $userId, string $message)
    {
        $data = [
            'user_id'    => $userId,
            'message'    => $message,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    /**
     * Mark all notifications as read for a specific user
     * 
     * @param int $userId The user ID to mark all notifications as read
     * @return bool True if successful, false otherwise
     */
    public function markAllAsRead(int $userId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->set(['is_read' => 1])
                    ->update();
    }

    /**
     * Delete old read notifications (e.g., older than 30 days)
     * 
     * @param int $days Number of days to keep notifications (default: 30)
     * @return bool True if successful, false otherwise
     */
    public function deleteOldNotifications(int $days = 30): bool
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('is_read', 1)
                    ->where('created_at <', $date)
                    ->delete();
    }
}
