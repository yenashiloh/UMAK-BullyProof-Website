<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'cards';
    
    protected $fillable = [
        'title',
        'description',
        'buttons',
        'created_by',
        'status',
    ];
    
    protected $casts = [
        'buttons' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}