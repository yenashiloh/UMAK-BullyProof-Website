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
        'appointment_date',
        'appointment_start_time',
        'appointment_end_time',
        'created_at',
        'updated_at'
    ];
}