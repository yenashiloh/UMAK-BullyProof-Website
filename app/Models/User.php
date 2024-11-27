<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'fullname',
        'email',
        'contact',
        'password',
        'type',
        'status',
        '_v'
    ];

    protected $hidden = [
        'password',
    ];

    public function reportedReports()
    {
        return $this->hasMany(Report::class, 'victimName', 'fullname');
    }

    public function submittedReports()
    {
        return $this->hasMany(Report::class, 'reportedBy');
    }
}
