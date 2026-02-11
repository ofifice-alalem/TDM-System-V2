<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function create($userId, $type, $title, $message, $link = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }

    public function markAsRead($notificationId)
    {
        return Notification::where('id', $notificationId)->update(['is_read' => true]);
    }

    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)->update(['is_read' => true]);
    }

    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)->where('is_read', false)->count();
    }

    public function getRecent($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
