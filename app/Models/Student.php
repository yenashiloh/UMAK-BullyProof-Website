<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class Student extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'students';
    
    protected $fillable = [
     'name',
     'email',
     'schoolId',
     'status',
     'department_emails',
    ];

}