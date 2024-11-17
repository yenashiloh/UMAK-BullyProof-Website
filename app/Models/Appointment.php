<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'appointments';
    
    protected $fillable = [
        'respondent_name',
        'respondent_email',
        'complainant_name',
        'complainant_email',
        'appointment_datetime',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $dates = [
        'appointment_datetime',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'appointment_datetime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}