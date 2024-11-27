<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class EmailContent extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'emailContent';
    
    protected $fillable = [
        'complainant_email_content',
        'complainee_email_content',
        'cancelled_email_content',
        'reschedule_email_content',
        'created_at',
        'updated_at'
    ];

    // Override the table name getter to ensure we're using the right collection
    public function getTable()
    {
        return $this->collection;
    }
}