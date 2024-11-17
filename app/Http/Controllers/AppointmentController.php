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

class AppointmentController extends Controller
{
    //show appointment page
    public function showAppointmentPage()
    {
        $client = new Client(env('MONGODB_URI'));
        $adminCollection = $client->bullyproof->admins;
    
        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    
        $appointments = $this->getAppointments();
    
        return view('admin.appointment.appointment', compact(
            'firstName', 
            'lastName', 
            'email',
            'appointments' 
        ));
    }
    
    //get the appointment
    private function getAppointments()
    {
        try {
            $appointments = Appointment::all();
            
            return $appointments->map(function ($appointment) {
                $startTime = Carbon::parse($appointment->appointment_datetime);
                
                return [
                    'id' => (string) $appointment->_id,
                    'title' => $appointment->respondent_name,
                    'start' => $startTime->format('Y-m-d\TH:i:s'),
                    'end' => $startTime->addHours(1)->format('Y-m-d\TH:i:s'),
                    'description' => $appointment->complainant_name,
                    'status' => $appointment->status ?? 'Waiting for Confirmation',
                    'className' => 'event-' . strtolower($appointment->status ?? 'waiting-for-confirmation'),
                    'respondent_email' => $appointment->respondent_email,  
                    'complainant_email' => $appointment->complainant_email  
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error fetching appointments: ' . $e->getMessage());
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
            $query->whereBetween('appointment_datetime', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }
    
        if ($status && $status !== 'All') {
            $query->where('status', $status);
        }
    
        $appointments = $query->get();
    
        $filteredAppointments = $appointments->map(function ($appointment) {
            return [
                'id' => (string) $appointment->_id,
                'title' => $appointment->respondent_name,
                'start' => $appointment->appointment_datetime,
                'description' => $appointment->complainant_name,
                'status' => $appointment->status,
                'respondent_email' => $appointment->respondent_email,
                'complainant_email' => $appointment->complainant_email,
            ];
        });
    
        return response()->json(['appointments' => $filteredAppointments]);
    }

    //store appointment
    public function storeAppointment(Request $request)
    {
        try {
            $validated = $request->validate([
                'respondent_name' => 'required|string|max:255',
                'respondent_email' => 'required|email|max:255',
                'complainant_name' => 'required|string|max:255',
                'complainant_email' => 'required|email|max:255',
                'appointment_date' => 'required|date',  
                'appointment_time' => 'required|date_format:H:i',  
            ]);
    
            $appointmentDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_time']);
            
            $document = [
                'respondent_name' => $validated['respondent_name'],
                'respondent_email' => $validated['respondent_email'],
                'complainant_name' => $validated['complainant_name'],
                'complainant_email' => $validated['complainant_email'],
                'appointment_datetime' => $appointmentDateTime,  
                'status' => 'Waiting for Confirmation',
                'created_at' => new UTCDateTime(now()),
                'updated_at' => new UTCDateTime(now()),
            ];
    
            $appointment = new Appointment($document);
            $appointment->save();
    
            // Send email to complainant
            Mail::to($validated['complainant_email'])
                ->send(new AppointmentNotification($document));
    
            // Send email to respondent
            Mail::to($validated['respondent_email'])
                ->send(new AppointmentNotification($document));
    
            return response()->json([
                'success' => true,
                'message' => 'Appointment created successfully!',
                'appointment_id' => (string)$appointment->_id
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
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

    //appointment change status
    public function changeStatus(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|string',
            'new_status' => 'required|string|in:Waiting for Confirmation,Approved,Cancelled,Missed,Done',
        ]);

        try {
            $appointment = Appointment::find($validated['appointment_id']);

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found'
                ], 404);
            }

            $appointment->status = $validated['new_status'];
            $appointment->updated_at = new \MongoDB\BSON\UTCDateTime(now()); 
            $appointment->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!',
                'new_status' => $appointment->status
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating appointment status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }
}