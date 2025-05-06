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
        '_id',
        'title',
        'description',
        'created_by',
        'status',
        'card_id',
        'steps',
        'updated_at',
        'created_at'
    ];

    protected $casts = [
        'steps' => 'array',
        'updated_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    public function formData()
    {
        return $this->hasMany(FormData::class, 'form_builder_id', '_id');
    }

    public function formElements()
    {
        return $this->hasMany(FormElement::class, 'form_builder_id', '_id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', '_id');
    }
}