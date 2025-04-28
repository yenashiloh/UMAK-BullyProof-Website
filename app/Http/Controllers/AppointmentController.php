<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;
use App\Mail\AppointmentNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\ObjectId;
use App\Models\EmailContent;
use App\Models\Report;
use App\Models\User;
use App\Models\Student;
use App\Models\DepartmentEmail;


class AppointmentController extends Controller
{
    protected $collection;

    public function __construct()
    {
        $client = new Client(env('MONGODB_URI'));
        $database = $client->selectDatabase(env('DB_DATABASE', 'bullyproof'));
        $this->collection = $database->appointments;
    }
    
    //show appointment page
    public function showAppointmentPage()
    {
        $client = new Client(env('MONGODB_URI'));
        $adminCollection = $client->bullyproof->admins;
    
        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new ObjectId($adminId)]);
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    
        $appointments = $this->getCalendar();
        
        $appointmentsJson = json_encode($appointments);
    
        return view('admin.appointment.appointment', compact(
            'firstName', 
            'lastName', 
            'email',
            'appointments',
            'appointmentsJson'
        ));
    }
    
    //get appointment for calendar
    private function getCalendar()
    {
        try {
            $appointments = Appointment::all();
            
            return $appointments->map(function ($appointment) {
                $appointmentDate = Carbon::parse($appointment->appointment_date);
                $startTime = Carbon::parse($appointment->appointment_start_time);
                $endTime = Carbon::parse($appointment->appointment_end_time);
                
                $eventStart = Carbon::create(
                    $appointmentDate->year,
                    $appointmentDate->month,
                    $appointmentDate->day,
                    $startTime->hour,
                    $startTime->minute,
                    0
                );
                
                $eventEnd = Carbon::create(
                    $appointmentDate->year,
                    $appointmentDate->month,
                    $appointmentDate->day,
                    $endTime->hour,
                    $endTime->minute,
                    0
                );
                
                // Format the status class name
                $status = $appointment->status ?? 'Waiting for Confirmation';
                $statusClass = 'event-' . str_replace(' ', '-', strtolower($status));
                
                return [
                    'id' => (string) $appointment->_id,
                    'title' => $appointment->respondent_name,
                    'start' => $eventStart->format('Y-m-d\TH:i:s'),
                    'end' => $eventEnd->format('Y-m-d\TH:i:s'),
                    'description' => 'Complainant: ' . $appointment->complainant_name,
                    'status' => $status,
                    'className' => $statusClass,
                    'respondent_email' => $appointment->respondent_email,
                    'complainant_email' => $appointment->complainant_email,
                    'complainant_department_email' => $appointment->complainant_department_email,
                    'complainee_department_email' => $appointment->complainee_department_email,
                    'respondent_name' => $appointment->respondent_name,
                    'complainant_name' => $appointment->complainant_name,
                    'appointment_end_time' => $appointment->appointment_end_time,  
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error fetching appointments: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    //get appointments data
    private function getAppointments()
    {
        try {
            $appointments = Appointment::all();

            $appointments = $appointments->sortBy(function ($appointment) {
                return Carbon::parse($appointment->appointment_date);
            });
            
            return $appointments->map(function ($appointment) {
                $appointmentDate = Carbon::parse($appointment->appointment_date);
                $startTime = Carbon::parse($appointment->appointment_start_time);
                $endTime = Carbon::parse($appointment->appointment_end_time);
                
                $eventStart = Carbon::create(
                    $appointmentDate->year,
                    $appointmentDate->month,
                    $appointmentDate->day,
                    $startTime->hour,
                    $startTime->minute,
                    0
                );
                
                $eventEnd = Carbon::create(
                    $appointmentDate->year,
                    $appointmentDate->month,
                    $appointmentDate->day,
                    $endTime->hour,
                    $endTime->minute,
                    0
                );
                
                $status = $appointment->status ?? 'Waiting for Confirmation';
                $statusClass = 'event-' . str_replace(' ', '-', strtolower($status));
                
                return [
                    'id' => (string) $appointment->_id,
                    'title' => $appointment->respondent_name,
                    'start' => $eventStart->format('Y-m-d\TH:i:s'),
                    'end' => $eventEnd->format('Y-m-d\TH:i:s'),
                    'description' =>$appointment->complainant_name,
                    'status' => $status,
                    'className' => $statusClass,
                    'respondent_email' => $appointment->respondent_email,
                    'complainant_email' => $appointment->complainant_email,
                    'complainant_department_email' => $appointment->complainant_department_email,
                    'complainee_department_email' => $appointment->complainee_department_email,
                    'respondent_name' => $appointment->respondent_name,
                    'complainant_name' => $appointment->complainant_name,
                    'appointment_end_time' => $appointment->appointment_end_time,   
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error fetching appointments: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
    
    //filter appointment summary
    public function filterAppointments(Request $request)
    {
        $status = $request->input('status');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
    
        $query = Appointment::query();
    
        if ($startDate && $endDate) {
            $query->whereBetween('appointment_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }
    
        if ($status && $status !== 'All') {
            $query->where('status', $status);
        }
    
        $appointments = $query->get();
    
        $data = [
            'appointments' => $appointments->map(function ($appointment) {
                return [
                    'id' => (string) $appointment->_id,
                    'appointment_date' => $appointment->appointment_date->format('F j, Y'),
                    'appointment_start_time' => $appointment->appointment_start_time,
                    'appointment_end_time' => $appointment->appointment_end_time,
                    'respondent_name' => $appointment->respondent_name,
                    'respondent_email' => $appointment->respondent_email,
                    'complainant_name' => $appointment->complainant_name,
                    'complainant_email' => $appointment->complainant_email,
                    'status' => $appointment->status,
                ];
            })->all()
        ];
    
        return response()->json($data);
    }
    //store appointment
    public function storeAppointment(Request $request)
    {
        // Log the incoming request details
        Log::info('storeAppointment method called', [
            'url' => $request->url(),
            'method' => $request->method(),
            'input' => $request->all(),
            'headers' => $request->headers->all(),
        ]);
    
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'report_id' => 'required|string',
                'appointment_date' => 'required|date',
                'appointment_start_time' => 'required|date_format:H:i',
                'appointment_end_time' => 'required|date_format:H:i|after:appointment_start_time',
            ]);
            
            if ($validator->fails()) {
                Log::warning('Validation failed', ['errors' => $validator->errors()]);
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            
            $validated = $validator->validated();
            Log::info('Validation passed', ['validated_data' => $validated]);
            
            // Get report details
            $report = Report::where('_id', new ObjectId($validated['report_id']))->first();
            
            if (!$report) {
                Log::error('Report not found', ['report_id' => $validated['report_id']]);
                return response()->json([
                    'success' => false,
                    'errors' => ['report_id' => ['Report not found']]
                ], 404);
            }
            Log::info('Report found', ['report_id' => $validated['report_id']]);
            
            // Get complainant (victim/reporter) details from users collection
            $complainant = User::where('_id', new ObjectId($report->reportedBy))->first();
            
            if (!$complainant) {
                Log::error('Complainant not found', ['reportedBy' => $report->reportedBy]);
                return response()->json([
                    'success' => false,
                    'errors' => ['complainant' => ['Complainant not found']]
                ], 404);
            }
            Log::info('Complainant found', ['complainant_id' => $report->reportedBy]);
            
            // Get respondent (perpetrator) details from users collection using the improved name matching
            $respondent = $this->findUserByName($report->perpetratorName);
            
            if (!$respondent) {
                Log::error('Respondent not found', ['perpetratorName' => $report->perpetratorName]);
                return response()->json([
                    'success' => false,
                    'errors' => ['respondent' => ['Respondent not found']]
                ], 404);
            }
            Log::info('Respondent found', ['respondent_name' => $report->perpetratorName, 'matched_to' => $respondent->fullname]);
            
            // Get department emails
            $complainantDepartmentObj = null;
            if (isset($complainant->department_emails)) {
                $complainantDepartmentObj = DepartmentEmail::where('_id', new ObjectId($complainant->department_emails))->first();
            }
            
            $respondenttDepartmentObj = null;
            if (isset($respondent->department_emails)) {
                $respondenttDepartmentObj = DepartmentEmail::where('_id', new ObjectId($respondent->department_emails))->first();
            }
            
            $complainantDeptEmail = $complainantDepartmentObj ? $complainantDepartmentObj->email : null;
            $respondentDeptEmail = $respondenttDepartmentObj ? $respondenttDepartmentObj->email : null;
            
            // Search for department emails in students collection
            // For complainant - find matching student record by name
            $complainantStudent = Student::where('name', 'LIKE', '%' . $complainant->fullname . '%')->first();
            if ($complainantStudent && isset($complainantStudent->department_emails)) {
                $deptEmailId = $complainantStudent->department_emails;
                $complainantDepartmentObj = DepartmentEmail::where('_id', new ObjectId($deptEmailId))->first();
                $complainantDeptEmail = $complainantDepartmentObj ? $complainantDepartmentObj->email : null;
                Log::info('Complainant department email found from students collection', [
                    'student_name' => $complainantStudent->name,
                    'department_email_id' => $deptEmailId,
                    'email' => $complainantDeptEmail
                ]);
            } else {
                Log::warning('No matching student record found for complainant', [
                    'complainant_name' => $complainant->fullname
                ]);
            }
            
            // For respondent - find matching student record by name
            $respondentStudent = Student::where('name', 'LIKE', '%' . $respondent->fullname . '%')->first();
            if ($respondentStudent && isset($respondentStudent->department_emails)) {
                $deptEmailId = $respondentStudent->department_emails;
                $respondentDepartmentObj = DepartmentEmail::where('_id', new ObjectId($deptEmailId))->first();
                $respondentDeptEmail = $respondentDepartmentObj ? $respondentDepartmentObj->email : null;
                Log::info('Respondent department email found from students collection', [
                    'student_name' => $respondentStudent->name,
                    'department_email_id' => $deptEmailId,
                    'email' => $respondentDeptEmail
                ]);
            } else {
                Log::warning('No matching student record found for respondent', [
                    'respondent_name' => $respondent->fullname
                ]);
            }
            
            Log::info('Department emails retrieved', [
                'complainant_dept_email' => $complainantDeptEmail,
                'respondent_dept_email' => $respondentDeptEmail
            ]);
            
            // Check for overlapping appointments
            $startDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_start_time']);
            $endDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_end_time']);
            
            $overlappingAppointments = Appointment::where('appointment_date', new UTCDateTime($startDateTime->timestamp * 1000))
                ->where(function($query) use ($validated) {
                    $query->where(function($q) use ($validated) {
                        $q->where('appointment_start_time', '<', $validated['appointment_end_time'])
                          ->where('appointment_end_time', '>', $validated['appointment_start_time']);
                    });
                })
                ->whereNotIn('status', ['Cancelled'])
                ->count();
            
            if ($overlappingAppointments > 0) {
                Log::warning('Overlapping appointment found', ['count' => $overlappingAppointments]);
                return response()->json([
                    'success' => false,
                    'errors' => ['appointment_date' => ['This time slot is already booked']]
                ], 422);
            }
            Log::info('No overlapping appointments');
            
            $now = Carbon::now();
            $appointmentDate = new UTCDateTime($startDateTime->timestamp * 1000);
            
            // Create appointment
            $appointment = new Appointment([
                'respondent_name' => $respondent->fullname,
                'respondent_email' => $respondent->email,
                'complainee_department_email' => $respondentDeptEmail,
                'complainant_name' => $complainant->fullname,
                'complainant_email' => $complainant->email,
                'complainant_department_email' => $complainantDeptEmail,
                'appointment_date' => $appointmentDate,
                'appointment_start_time' => $validated['appointment_start_time'],
                'appointment_end_time' => $validated['appointment_end_time'],
                'status' => 'Waiting for Confirmation',
                'created_at' => new UTCDateTime($now->timestamp * 1000),
                'updated_at' => new UTCDateTime($now->timestamp * 1000),
                'reports_id' => new ObjectId($validated['report_id'])
            ]);
            
            $appointment->save();
            Log::info('Appointment saved', ['appointment_id' => (string)$appointment->_id]);
            
            // Update report status
            $report->status = "Under Investigation";
            $report->save();
            Log::info('Report status updated to Under Investigation', ['report_id' => $validated['report_id']]);
            
            // Send emails
            try {
                $emailContent = EmailContent::first();
                
                // Send email to complainant and their department
                if ($complainant->email) {
                    Mail::to($complainant->email)
                        ->send(new AppointmentNotification($appointment, true)); // true = complainant
                    Log::info('Email sent to complainant', ['email' => $complainant->email]);
                }
                
                if ($complainantDeptEmail) {
                    Mail::to($complainantDeptEmail)
                        ->send(new AppointmentNotification($appointment, 'complainant_department'));
                    Log::info('Email sent to complainant department', ['email' => $complainantDeptEmail]);
                }
                
                // Send email to respondent and their department
                if ($respondent->email) {
                    Mail::to($respondent->email)
                        ->send(new AppointmentNotification($appointment, false)); // false = respondent
                    Log::info('Email sent to respondent', ['email' => $respondent->email]);
                }
                
                if ($respondentDeptEmail) {
                    Mail::to($respondentDeptEmail)
                        ->send(new AppointmentNotification($appointment, 'complainee_department'));
                    Log::info('Email sent to respondent department', ['email' => $respondentDeptEmail]);
                }
                
                Log::info('Appointment creation successful', ['appointment_id' => (string)$appointment->_id]);
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment created successfully!',
                    'appointment_id' => (string)$appointment->_id
                ], 201);
            } catch (\Exception $e) {
                Log::error('Email sending failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment created, but email notifications failed.',
                    'appointment_id' => (string)$appointment->_id
                ], 201);
            }
        } catch (\Exception $e) {
            Log::error('Appointment creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get appointment details for a report
     */
    public function getAppointmentForReport($reportId)
    {
        try {
            $appointment = Appointment::where('reports_id', new ObjectId($reportId))->first();
            
            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No appointment found for this report'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'appointment' => $appointment
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    private function findUserByName($name)
    {
        Log::info('Finding user by name', ['searching_for' => $name]);
        
        // Remove special characters and extra spaces
        $cleanName = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name);
        $cleanName = trim(preg_replace('/\s+/', ' ', $cleanName));
        
        // Handle "Last, First" format
        $nameParts = explode(',', $name);
        if (count($nameParts) > 1) {
            $lastName = trim($nameParts[0]);
            $firstNameParts = explode(' ', trim($nameParts[1]));
            $firstName = trim($firstNameParts[0]);
            
            Log::info('Parsed name parts', ['firstName' => $firstName, 'lastName' => $lastName]);
            
            // Try different query combinations
            $user = User::where('fullname', 'LIKE', '%' . $firstName . '%' . $lastName . '%')
                ->orWhere('fullname', 'LIKE', '%' . $lastName . '%' . $firstName . '%')
                ->orWhere(function($query) use ($firstName, $lastName) {
                    $query->where('fullname', 'LIKE', '%' . $firstName . '%')
                          ->where('fullname', 'LIKE', '%' . $lastName . '%');
                })
                ->first();
            
            if ($user) {
                Log::info('User found by first/last name matching', ['matched_name' => $user->fullname]);
                return $user;
            }
        }
        
        // If no comma, try direct matches or name part matching
        $nameParts = explode(' ', $cleanName);
        if (count($nameParts) >= 2) {
            $firstName = $nameParts[0];
            $lastName = end($nameParts);
            
            Log::info('Trying with name parts', ['firstName' => $firstName, 'lastName' => $lastName]);
            
            $user = User::where('fullname', 'LIKE', '%' . $firstName . '%')
                ->where('fullname', 'LIKE', '%' . $lastName . '%')
                ->first();
            
            if ($user) {
                Log::info('User found by name parts matching', ['matched_name' => $user->fullname]);
                return $user;
            }
        }
        
        // Last resort: try each significant word in the name
        foreach ($nameParts as $part) {
            if (strlen($part) > 2) { // Only search with parts that are likely actual names
                Log::info('Trying with individual name part', ['part' => $part]);
                $user = User::where('fullname', 'LIKE', '%' . $part . '%')->first();
                if ($user) {
                    Log::info('User found by individual name part', ['part' => $part, 'matched_name' => $user->fullname]);
                    return $user;
                }
            }
        }
        
        Log::warning('No user found matching name', ['name' => $name]);
        return null;
    }

    //show appointment summary page
    public function showAppointmentSummaryPage()
    {
        $client = new Client(env('MONGODB_URI'));
        $adminCollection = $client->bullyproof->admins;
    
        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    
        $appointments = $this->getAppointments();
    
        return view('admin.appointment.summary', compact(
            'firstName', 
            'lastName', 
            'email',
            'appointments' 
        ));
    }

    //change status of appointment
    public function changeStatus(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|string',
            'new_status' => 'required|string|in:Waiting for Confirmation,Approved,Missed,Done,Rescheduled',
        ]);
    
        try {
            $appointment = Appointment::find($validated['appointment_id']);
    
            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found',
                ], 404);
            }
    
            $emailContent = EmailContent::orderBy('created_at', 'desc')->first();
    
            if (!$emailContent) {
                Log::error('No email content found in the database');
                throw new \Exception('Email content is missing');
            }
    
            $oldStatus = $appointment->status;
            $appointment->status = $validated['new_status'];
            $appointment->updated_at = new UTCDateTime(now()->timestamp * 1000);
            $appointment->save();
    
            if ($validated['new_status'] === 'Rescheduled') {
                try {
                    $content = $emailContent->reschedule_email_content;
    
                    if (empty($content)) {
                        throw new \Exception("Email content for 'Rescheduled' is empty");
                    }
    
                    $emailData = [
                        'appointment' => [
                            'respondent_name' => $appointment->respondent_name,
                            'complainant_name' => $appointment->complainant_name,
                            'appointment_date' => $appointment->appointment_date,
                            'appointment_start_time' => $appointment->appointment_start_time,
                            'appointment_end_time' => $appointment->appointment_end_time,
                            'status' => $appointment->status,
                        ],
                        'content' => $content,
                    ];
    
                    foreach (['complainant_email', 'respondent_email', 'complainant_department_email', 'complainee_department_email'] as $emailField) {
                        $recipient = $appointment->$emailField;
    
                        if ($recipient) {
                            Mail::send("emails.rescheduled", $emailData, function ($message) use ($recipient, $appointment) {
                                $message->to($recipient)
                                    ->subject("Appointment {$appointment->status}");
                            });
                        } else {
                            Log::warning("Missing email address for field: {$emailField}");
                        }
                    }
    
                } catch (\Exception $e) {
                    Log::error('Failed to send status change email notification', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
    
                    return response()->json([
                        'success' => true,
                        'message' => "Status updated but email notifications failed: {$e->getMessage()}",
                        'new_status' => $appointment->status,
                    ]);
                }
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!',
                'new_status' => $appointment->status
            ]);
            
    
        } catch (\Exception $e) {
            Log::error('Error updating appointment status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }
}