<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class FormElement extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'form_elements';

    protected $fillable = [
        '_id',
        'form_builder_id',
        'step_id',
        'element_type',
        'title',
        'position',
        'settings',
        'options',
        'is_required',
        'card_id',
        'updated_at',
        'created_at'
    ];

    protected $casts = [
        'settings' => 'array',
        'options' => 'array',
        'is_required' => 'boolean',
        'updated_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    public function formBuilder()
    {
        return $this->belongsTo(FormBuilder::class, 'form_builder_id', '_id');
    }
}
