<?php

namespace App\Http\Controllers;

use MongoDB\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtherReportController extends Controller
{
    public function showOtherReport()
    {
        try {
            $client = new Client(env('MONGODB_URI'));
            $adminCollection = $client->bullyproof->admins;
            $formDataCollection = $client->bullyproof->form_data;
            $usersCollection = $client->bullyproof->users;

            // Get admin details
            $adminId = session('admin_id');
            $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);

            $firstName = $admin->first_name ?? '';
            $lastName = $admin->last_name ?? '';
            $email = $admin->email ?? '';
            $contactNumber = $admin->contact_number ?? '';

            // Get all form data documents
            $formDocs = $formDataCollection->find([], ['sort' => ['reported_at' => -1]]);

            $reports = [];
            foreach ($formDocs as $doc) {
                // Convert MongoDB document to array
                $formData = (array) $doc;

                // Get the reported_by user ID from form_data
                $reportedById = $doc->reported_by ?? null;

                $complainantName = "Unknown";

                // If we have a reporter ID, look up the user in the users collection
                if ($reportedById) {
                    $user = $usersCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($reportedById)]);
                    if ($user) {
                        $complainantName = $user->fullname ?? ($user->first_name . ' ' . $user->last_name ?? "Unknown");
                    }
                }

                // Format reported_at date
                $formattedDate = 'N/A';
                if (isset($doc->reported_at)) {
                    if ($doc->reported_at instanceof \MongoDB\BSON\UTCDateTime) {
                        $dateTime = $doc->reported_at->toDateTime();
                        $formattedDate = $dateTime->format('F j, Y');
                    } else if (is_string($doc->reported_at)) {
                        try {
                            $dateTime = new \DateTime($doc->reported_at);
                            $formattedDate = $dateTime->format('F j, Y');
                        } catch (\Exception $e) {
                            $formattedDate = $doc->reported_at;
                        }
                    }
                }

                // Determine status class
                $status = $doc->status ?? 'For Review';
                $statusClass = $this->getStatusClass($status);

                // Add to reports array with necessary fields
                $reports[] = [
                    '_id' => (string) $doc->_id,
                    'reported_at' => $doc->reported_at,
                    'formatted_date' => $formattedDate,
                    'complainant_name' => $complainantName,
                    'status' => $status,
                    'status_class' => $statusClass,
                    'form_builder_id' => $doc->form_builder_id ?? '',
                    'step_id' => $doc->step_id ?? ''
                ];
            }

            return view('admin.reports.other-reports', compact(
                'firstName',
                'lastName',
                'email',
                'contactNumber',
                'reports'
            ));
        } catch (\Exception $e) {
            return redirect()->route('admin.reports.index')->with('error', 'Error loading reports: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to determine the status badge class
     */
    private function getStatusClass($status)
    {
        switch ($status) {
            case 'For Review':
                return 'primary';
            case 'Under Investigation':
                return 'warning text-white';
            case 'Resolved':
                return 'success';
            case 'Dismissed':
                return 'danger';
            case 'Reopened':
                return 'dark';
            case 'Awaiting Response':
                return 'secondary';
            case 'Withdrawn':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    public function viewReport($id)
    {
        try {
            $client = new Client(env('MONGODB_URI'));
            $formDataCollection = $client->bullyproof->form_data;
            $formBuildersCollection = $client->bullyproof->form_builders;
            $formElementsCollection = $client->bullyproof->form_elements;
            $usersCollection = $client->bullyproof->users;
            
            // Get the form data document
            $formData = $formDataCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
            if (!$formData) {
                return redirect()->route('admin.reports.view')->with('error', 'Report not found');
            }
            
            // Get the form builder details
            $formBuilder = $formBuildersCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($formData->form_builder_id)]);
            if (!$formBuilder) {
                return redirect()->route('admin.reports.other-reports')->with('error', 'Form builder not found');
            }
            
            // Get reporter details if available
            $reporter = null;
            $reporterData = null;
            if (isset($formData->reported_by)) {
                $reporter = $usersCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($formData->reported_by)]);
                $reporterData = $reporter ? json_decode(json_encode($reporter), true) : null;
            }
            
            // Process form builder steps
            $formBuilderData = json_decode(json_encode($formBuilder), true);
            $steps = [];
            
            if (isset($formBuilder->steps)) {
                if (is_string($formBuilder->steps)) {
                    $decodedSteps = json_decode($formBuilder->steps, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $steps = $decodedSteps;
                    } else {
                        preg_match_all('/\{"id":"([^"]+)","title":"([^"]+)"\}/', $formBuilder->steps, $matches, PREG_SET_ORDER);
                        foreach ($matches as $match) {
                            $steps[] = [
                                'id' => $match[1],
                                'title' => $match[2]
                            ];
                        }
                    }
                } else {
                    $steps = json_decode(json_encode($formBuilder->steps), true);
                }
            }
            
            if (!is_array($steps)) {
                $steps = [];
            }
            
            $formBuilderData['steps'] = $steps;
            
            // Get all form elements related to this form builder
            $formElements = [];
            foreach ($steps as $step) {
                $stepId = $step['id'] ?? '';
                if (empty($stepId)) continue;
                
                $elements = iterator_to_array($formElementsCollection->find([
                    'form_builder_id' => (string)$formData->form_builder_id,
                    'step_id' => $stepId
                ]));
                
                $elementsArray = [];
                foreach ($elements as $element) {
                    $elementArray = json_decode(json_encode($element), true);
                    $elementsArray[] = $elementArray;
                }
                $formElements[$stepId] = $elementsArray;
            }
            
            // Format reported_at date
            $reportedAt = '';
            if (isset($formData->reported_at)) {
                if ($formData->reported_at instanceof \MongoDB\BSON\UTCDateTime) {
                    $reportedAt = $formData->reported_at->toDateTime()->format('F j, Y, g:i a');
                } else if (is_string($formData->reported_at)) {
                    try {
                        $dateTime = new \DateTime($formData->reported_at);
                        $reportedAt = $dateTime->format('F j, Y, g:i a');
                    } catch (\Exception $e) {
                        $reportedAt = $formData->reported_at;
                    }
                }
            }
            
            // Process steps_data based on the actual structure in the database
            $formDataArray = json_decode(json_encode($formData), true);
            $stepsData = [];
            
            // Based on the screenshots, the steps_data structure is:
            // steps_data -> step-ID -> element_id -> value
            if (isset($formDataArray['steps_data']) && is_array($formDataArray['steps_data'])) {
                $stepsData = $formDataArray['steps_data'];
            }
            
            // Prepare the debug information for the view
            $debug = [];
            foreach ($steps as $step) {
                $stepId = $step['id'] ?? '';
                if (!empty($stepId)) {
                    $stepData = $stepsData[$stepId] ?? [];
                    $debug[$stepId] = [
                        'step_id' => $stepId,
                        'available_step_data' => $stepData
                    ];
                }
            }
            
            // Update formDataArray with the properly structured steps_data
            $formDataArray['steps_data'] = $stepsData;
            \Log::debug('Steps Data: ', [$formDataArray['steps_data']]);
            
            $adminId = session('admin_id');
            $admin = $client->bullyproof->admins->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
            $adminData = json_decode(json_encode($admin), true);
            
            $firstName = $adminData['first_name'] ?? '';
            $lastName = $adminData['last_name'] ?? '';
            $email = $adminData['email'] ?? '';
            $contactNumber = $adminData['contact_number'] ?? '';
            
            return view('admin.reports.view-other-report', compact(
                'formDataArray', 
                'formBuilderData', 
                'formElements', 
                'reporterData',
                'reportedAt',
                'firstName',
                'lastName',
                'email',
                'contactNumber',
                'debug'
            ));
        } catch (\Exception $e) {
            \Log::error('Error viewing report: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('admin.reports.other-reports')->with('error', 'Error viewing report: ' . $e->getMessage());
        }

        
    }

    /**
 * Add this function to your controller to help debug the options
 */
private function debugElementOptions($formElements)
    {
        $debug = [];
        
        foreach ($formElements as $stepId => $elements) {
            foreach ($elements as $element) {
                $elementId = $element['id'] ?? 'unknown';
                $elementType = $element['element_type'] ?? 'unknown';
                $title = $element['title'] ?? 'Untitled';
                
                if (isset($element['options'])) {
                    $options = $element['options'];
                    
                    // If options is a string, try to decode it
                    if (is_string($options)) {
                        $decodedOptions = json_decode($options, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $options = $decodedOptions;
                        } else {
                            // If JSON decode fails, try to parse it manually
                            preg_match_all('/\{"id":"([^"]+)","text":"([^"]+)"\}/', $options, $matches, PREG_SET_ORDER);
                            $parsedOptions = [];
                            foreach ($matches as $match) {
                                $parsedOptions[] = [
                                    'id' => $match[1],
                                    'text' => $match[2]
                                ];
                            }
                            if (!empty($parsedOptions)) {
                                $options = $parsedOptions;
                            }
                        }
                    }
                    
                    $debug[$stepId][$elementId] = [
                        'title' => $title,
                        'type' => $elementType,
                        'options' => $options
                    ];
                }
            }
        }
        
        // Log the debug info
        \Log::info('Element Options Debug: ' . json_encode($debug, JSON_PRETTY_PRINT));
        return $debug;
    }
    
}
