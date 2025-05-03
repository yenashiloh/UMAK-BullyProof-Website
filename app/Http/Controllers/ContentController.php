<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormBuilder;
use App\Models\FormElement;
use MongoDB\Client;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    /**
     * Display the content management page
     */
    public function showContentPage()
    {
        $client = new Client(env('MONGODB_URI'));
        $adminCollection = $client->bullyproof->admins;
    
        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        
        // Get all form builders
        $formBuilders = FormBuilder::where('created_by', $adminId)->get();
    
        return view('admin.content.content-management', compact(
            'firstName', 
            'lastName',
            'formBuilders'
        )); 
    }
    
    /**
     * Create a new form builder
     */
    public function createFormBuilder(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        $adminId = session('admin_id');
        
        // Create a new form builder with default first step
        $formBuilder = FormBuilder::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => $adminId,
            'status' => 'draft',
            'steps' => [
                [
                    'id' => 'step-' . uniqid(),
                    'title' => 'Step 1'
                ]
            ]
        ]);
        
        return response()->json([
            'success' => true,
            'form' => $formBuilder
        ]);
    }
    
    /**
     * Get a form builder by ID
     */
    public function getFormBuilder($id)
    {
        $formBuilder = FormBuilder::findOrFail($id);
        $elements = FormElement::where('form_builder_id', $id)
            ->orderBy('position', 'asc')
            ->get();
        
        return response()->json([
            'success' => true,
            'form' => $formBuilder,
            'elements' => $elements
        ]);
    }
    
    /**
     * Add a new step to a form builder
     */
    public function addStep($formId)
    {
        $formBuilder = FormBuilder::findOrFail($formId);
        $steps = $formBuilder->steps ?? [];
        
        $newStep = [
            'id' => 'step-' . uniqid(),
            'title' => 'Step ' . (count($steps) + 1)
        ];
        
        $steps[] = $newStep;
        
        $formBuilder->update(['steps' => $steps]);
        
        return response()->json([
            'success' => true,
            'step' => $newStep
        ]);
    }
    
    /**
     * Add a new element to a form
     */
    public function addElement(Request $request)
    {
        $request->validate([
            'form_builder_id' => 'required|string',
            'step_id' => 'required|string',
            'element_type' => 'required|string|in:section,paragraph,multiple_choice,checkbox,dropdown,file_upload',
            'position' => 'required|integer'
        ]);
        
        // Default settings for each element type
        $settings = [];
        $options = [];
        
        if ($request->element_type === 'file_upload') {
            $settings = [
                'allow_specific_types' => true,
                'file_types' => ['pdf', 'image'],
                'max_files' => 1,
                'max_file_size' => 10 // MB
            ];
        }
        
        if (in_array($request->element_type, ['multiple_choice', 'checkbox', 'dropdown'])) {
            $options = [
                ['id' => 'opt-' . uniqid(), 'text' => 'Option 1'],
                ['id' => 'opt-' . uniqid(), 'text' => 'Option 2'],
                ['id' => 'opt-' . uniqid(), 'text' => 'Option 3']
            ];
        }
        
        $element = FormElement::create([
            'form_builder_id' => $request->form_builder_id,
            'step_id' => $request->step_id,
            'element_type' => $request->element_type,
            'title' => $this->getDefaultTitle($request->element_type),
            'position' => $request->position,
            'settings' => $settings,
            'options' => $options,
            'is_required' => false
        ]);
        
        return response()->json([
            'success' => true,
            'element' => $element
        ]);
    }
    
    /**
     * Update an element
     */
    public function updateElement(Request $request, $id)
    {
        $element = FormElement::findOrFail($id);
        
        $data = $request->only(['title', 'content', 'is_required', 'position']);
        $element->update($data);
        
        return response()->json([
            'success' => true,
            'element' => $element
        ]);
    }
    
    /**
     * Delete an element
     */
    public function deleteElement($id)
    {
        $element = FormElement::findOrFail($id);
        $element->delete();
        
        return response()->json([
            'success' => true
        ]);
    }
    
    /**
     * Duplicate an element
     */
    public function duplicateElement($id)
    {
        $element = FormElement::findOrFail($id);
        
        // Create a duplicate with a new ID
        $duplicate = $element->replicate();
        $duplicate->title = $duplicate->title . ' (Copy)';
        $duplicate->position = $duplicate->position + 1;
        $duplicate->save();
        
        return response()->json([
            'success' => true,
            'element' => $duplicate
        ]);
    }
    
    /**
     * Update element options (for multiple choice, checkbox, dropdown)
     */
    public function updateElementOptions(Request $request, $id)
    {
        $element = FormElement::findOrFail($id);
        
        $element->update([
            'options' => $request->options
        ]);
        
        return response()->json([
            'success' => true,
            'options' => $element->options
        ]);
    }
    
    /**
     * Update file upload settings
     */
    public function updateFileUploadSettings(Request $request, $id)
    {
        $element = FormElement::findOrFail($id);
        
        $settings = [
            'allow_specific_types' => $request->allow_specific_types,
            'file_types' => $request->file_types,
            'max_files' => $request->max_files,
            'max_file_size' => $request->max_file_size
        ];
        
        $element->update([
            'settings' => $settings
        ]);
        
        return response()->json([
            'success' => true,
            'settings' => $element->settings
        ]);
    }
    
    /**
     * Get default title for element type
     */
    private function getDefaultTitle($elementType)
    {
        switch ($elementType) {
            case 'section':
                return 'Section Title';
            case 'paragraph':
                return 'Paragraph';
            case 'multiple_choice':
                return 'Choose an option';
            case 'checkbox':
                return 'Select all that apply';
            case 'dropdown':
                return 'Select from dropdown';
            case 'file_upload':
                return 'Upload Files';
            default:
                return 'Question';
        }
    }
}