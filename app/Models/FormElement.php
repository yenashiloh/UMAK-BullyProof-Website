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
        'form_builder_id',
        'step_id',
        'element_type', // 'section', 'paragraph', 'multiple_choice', 'checkbox', 'dropdown', 'file_upload'
        'title',
        'position',
        'settings',
        'options',
        'is_required',
    ];
    
    protected $casts = [
        'settings' => 'array',
        'options' => 'array',
        'is_required' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function formBuilder()
    {
        return $this->belongsTo(FormBuilder::class);
    }
}