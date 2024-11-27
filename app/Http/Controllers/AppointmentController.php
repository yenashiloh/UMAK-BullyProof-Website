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
        try {
            $validator = Validator::make($request->all(), [
                'respondent_name' => 'required|string|max:255',
                'respondent_email' => 'required|email',
                'complainant_name' => 'required|string|max:255',
                'complainant_email' => 'required|email',
                'appointment_date' => 'required|date',
                'appointment_start_time' => 'required|date_format:H:i',
                'appointment_end_time' => 'required|date_format:H:i|after:appointment_start_time',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $validated = $validator->validated();
    
            $startDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_start_time']);
            $endDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_end_time']);
    
            $overlappingAppointments = $this->collection->countDocuments([
                'appointment_date' => new UTCDateTime($startDateTime->timestamp * 1000),
                '$or' => [
                    [
                        'appointment_start_time' => [
                            '$lt' => $validated['appointment_end_time']
                        ],
                        'appointment_end_time' => [
                            '$gt' => $validated['appointment_start_time']
                        ]
                    ]
                ],
                'status' => [
                    '$nin' => ['Cancelled']
                ]
            ]);
    
            if ($overlappingAppointments > 0) {
                return response()->json([
                    'success' => false,
                    'errors' => ['appointment_date' => ['This time slot is already booked']]
                ], 422);
            }
    
            $now = Carbon::now();
            $startDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_start_time']);
            $appointmentDate = new UTCDateTime($startDateTime->timestamp * 1000);
    
            $document = [
                'respondent_name' => $validated['respondent_name'],
                'respondent_email' => $validated['respondent_email'],
                'complainant_name' => $validated['complainant_name'],
                'complainant_email' => $validated['complainant_email'],
                'appointment_date' => $appointmentDate,
                'appointment_start_time' => $validated['appointment_start_time'],
                'appointment_end_time' => $validated['appointment_end_time'],
                'status' => 'Waiting for Confirmation',
                'created_at' => new UTCDateTime($now->timestamp * 1000),
                'updated_at' => new UTCDateTime($now->timestamp * 1000)
            ];
    
            $result = $this->collection->insertOne($document);

            if ($result->getInsertedCount() > 0) {
                try {
                    // Send email to complainant
                    Mail::to($validated['complainant_email'])
                        ->send(new AppointmentNotification($document, true));

                    // Send email to respondent
                    Mail::to($validated['respondent_email'])
                        ->send(new AppointmentNotification($document, false));

                    return response()->json([
                        'success' => true,
                        'message' => 'Appointment created successfully!',
                        'appointment_id' => (string)$result->getInsertedId()
                    ], 201);

                } catch (\Exception $e) {
                    Log::error('Email sending failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Appointment created, but email notifications failed.',
                        'appointment_id' => (string)$result->getInsertedId()
                    ], 201);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Appointment creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment'
            ], 500);
        }
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
            'new_status' => 'required|string|in:Waiting for Confirmation,Approved,Cancelled,Missed,Done,Rescheduled',
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
    
            if (in_array($validated['new_status'], ['Cancelled', 'Rescheduled'])) {
                try {
                    $template = $validated['new_status'] === 'Cancelled' ? 'cancelled' : 'rescheduled';
                    $content = $validated['new_status'] === 'Cancelled' 
                        ? $emailContent->cancelled_email_content 
                        : $emailContent->reschedule_email_content;
    
                    if (empty($content)) {
                        throw new \Exception("Email content for '{$validated['new_status']}' is empty");
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
    
                    foreach (['complainant_email', 'respondent_email'] as $emailField) {
                        $recipient = $appointment->$emailField;
    
                        if ($recipient) {
                            Mail::send("emails.{$template}", $emailData, function ($message) use ($recipient, $appointment) {
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
                        'template' => $template ?? null,
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