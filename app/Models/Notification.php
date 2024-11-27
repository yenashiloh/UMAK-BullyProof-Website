<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Notification extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'notifications';

    protected $fillable = [
        'userId',
        'reportId',
        'type',
        'message',
        'status',
        'createdAt',
        'readAt'
    ];

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Relationship with Report model
    public function report()
    {
        return $this->belongsTo(Report::class, 'reportId');
    }
}
