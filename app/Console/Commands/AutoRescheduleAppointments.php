<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\EmailContent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MongoDB\BSON\UTCDateTime;

class AutoRescheduleAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:auto-reschedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically reschedule appointments that are past their date/time and still in Waiting for Confirmation status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Find appointments that are:
        // 1. In "Waiting for Confirmation" status
        // 2. Have a date and end time that has already passed
        $expiredAppointments = Appointment::where('status', 'Waiting for Confirmation')
            ->where(function($query) use ($now) {
                $query->where('appointment_date', '<', $now->format('Y-m-d'))
                    ->orWhere(function($query) use ($now) {
                        $query->where('appointment_date', '=', $now->format('Y-m-d'))
                              ->where('appointment_end_time', '<', $now->format('H:i:s'));
                    });
            })
            ->get();
            
        if ($expiredAppointments->isEmpty()) {
            $this->info('No expired appointments found that need rescheduling.');
            return 0;
        }
        
        $emailContent = EmailContent::orderBy('created_at', 'desc')->first();
        
        if (!$emailContent) {
            $this->error('No email content found in the database');
            Log::error('No email content found when auto-rescheduling appointments');
            return 1;
        }
        
        $updatedCount = 0;
        
        foreach ($expiredAppointments as $appointment) {
            try {
                $oldStatus = $appointment->status;
                $appointment->status = 'Rescheduled';
                $appointment->updated_at = new UTCDateTime(now()->timestamp * 1000);
                $appointment->save();
                
                $updatedCount++;
                
                // Send email notifications
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
                            Log::warning("Missing email address for field: {$emailField} during auto-reschedule");
                        }
                    }
                    
                } catch (\Exception $e) {
                    Log::error('Failed to send auto-reschedule email notification', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'appointment_id' => $appointment->id
                    ]);
                }
                
                $this->info("Updated appointment ID: {$appointment->id} from '{$oldStatus}' to 'Rescheduled'");
                
            } catch (\Exception $e) {
                Log::error('Error during auto-rescheduling of appointment', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'appointment_id' => $appointment->id
                ]);
                
                $this->error("Failed to update appointment ID: {$appointment->id}: {$e->getMessage()}");
            }
        }
        
        $this->info("Successfully rescheduled {$updatedCount} expired appointment(s).");
        return 0;
    }
}