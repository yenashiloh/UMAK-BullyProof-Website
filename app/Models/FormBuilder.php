<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class FormBuilder extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'form_builders';
    
    protected $fillable = [
        'title',
        'description',
        'steps',
        'created_by',
        'status',
    ];
    
    protected $casts = [
        'steps' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function elements()
    {
        return $this->hasMany(FormElement::class);
    }
}