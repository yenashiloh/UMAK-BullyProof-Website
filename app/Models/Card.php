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
        '_id',
        'title',
        'description',
        'buttons',
        'created_by',
        'status',
        'updated_at',
        'created_at'
    ];

    protected $casts = [
        'buttons' => 'array',
        'updated_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    public function formBuilders()
    {
        return $this->hasMany(FormBuilder::class, 'card_id', '_id');
    }
}