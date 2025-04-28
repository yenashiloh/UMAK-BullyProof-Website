<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Carbon\Carbon;

class DepartmentEmail extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'department_emails';
    
    protected $fillable = [
       'email',
       'department'
    ];
}