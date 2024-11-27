<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Report;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class NotificationService
{
    public function createStatusChangeNotification($reportId, $oldStatus, $newStatus)
    {
        $report = Report::find($reportId);
        
        if (!$report) {
            throw new \Exception('Report not found');
        }

        $victim = User::where('fullname', $report->victimName)->first();

        if ($victim) {
            return Notification::create([
                'userId' => $victim->_id,
                'reportId' => new ObjectId($reportId),
                'type' => 'STATUS_CHANGE',
                'message' => "Your report status has been updated from {$oldStatus} to {$newStatus}.",
                'status' => 'unread',
                'createdAt' => new UTCDateTime(),
                'readAt' => null
            ]);
        }
    }
}
