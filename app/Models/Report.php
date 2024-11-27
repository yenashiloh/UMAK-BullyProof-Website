<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Report extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'reports';

    protected $fillable = [
        'victimRelationship',
        'victimName',
        'victimType',
        'gradeYearLevel',
        'hasReportedBefore',
        'reportedTo',
        'platformUsed',
        'incidentDetails',
        'incidentEvidence',
        'perpetratorName',
        'perpetratorRole',
        'perpetratorGradeYearLevel',
        'supportTypes',
        'actionsTaken',
        'describeActions',
        'reportedBy',
        'status',
        'reportDate',
        '_v'
    ];

    protected $casts = [
        'platformUsed' => 'array',
        'incidentEvidence' => 'array',
        'supportTypes' => 'array',
        'reportDate' => 'datetime'
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reportedBy');
    }

    public function victim()
    {
        return $this->belongsTo(User::class, 'victimName', 'fullname');
    }

    public static function getStatusOptions()
    {
        return [
            'For Review',
            'Under Investigation',
            'Resolved'
        ];
    }

    public static function getPlatformOptions()
    {
        return [
            'Facebook',
            'Twitter',
            'Instagram',
            'TikTok',
            'YouTube',
            'WhatsApp',
            'SMS',
            'Other'
        ];
    }

    public static function getSupportTypeOptions()
    {
        return [
            'Counseling',
            'Mediation',
            'Parent Conference',
            'Disciplinary Action',
            'Other'
        ];
    }
}
