<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class FormData extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'form_data';
    
    // No need for timestamps as they aren't in the JSON structure
    public $timestamps = false;
    
    protected $fillable = [
        '_id',
        'form_builder_id', 
        'step_id',
        'data',       // This will contain array of form_element_id and value pairs
        'reported_by',
        'status',
        'reported_at'
    ];
    
    protected $casts = [
        'data' => 'array',
        'reported_at' => 'datetime'
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];
    
    /**
     * This indicates that the value inside data can be either a scalar value or an array
     */
    public function formBuilder()
    {
        return $this->belongsTo(FormBuilder::class, 'form_builder_id', '_id');
    }
    
    /**
     * Get a form element value by ID
     * The value can be either a scalar (string, number) or an array
     * 
     * @param string $form_element_id
     * @return mixed|null The value (string, number, array) or null if not found
     */
    public function getElementValue($form_element_id)
    {
        foreach ($this->data as $item) {
            if ($item['form_element_id'] === $form_element_id) {
                return $item['value'];
            }
        }
        
        return null;
    }
    
    /**
     * Set a form element value
     * The value can be either a scalar (string, number) or an array
     * 
     * @param string $form_element_id
     * @param mixed $value
     * @return $this
     */
    public function setElementValue($form_element_id, $value)
    {
        $found = false;
        
        // Update existing element if found
        foreach ($this->data as $key => $item) {
            if ($item['form_element_id'] === $form_element_id) {
                $this->data[$key]['value'] = $value;
                $found = true;
                break;
            }
        }
        
        // Add new element if not found
        if (!$found) {
            $this->data[] = [
                'form_element_id' => $form_element_id,
                'value' => $value
            ];
        }
        
        return $this;
    }
    
    /**
     * Determine if a value is multi-value (array) or single value
     * 
     * @param string $form_element_id
     * @return bool
     */
    public function isMultiValue($form_element_id)
    {
        $value = $this->getElementValue($form_element_id);
        return is_array($value);
    }
}