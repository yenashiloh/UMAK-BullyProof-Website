<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;

class AppointmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $appointmentData;
    public $recipientType;
    private $emailContent;

    public function __construct($appointmentData, $recipientType = true)
    {
        $this->appointmentData = $appointmentData;
        $this->recipientType = $recipientType;
        
        $client = new Client(env('MONGODB_URI'));
        $database = $client->selectDatabase(env('DB_DATABASE', 'bullyproof'));
        $this->emailContent = $database->emailContent;
    }

    private function getLatestEmailContent()
    {
        try {
            $latestEmail = $this->emailContent
                ->findOne(
                    [],
                    [
                        'sort' => ['created_at' => -1],
                        'projection' => [
                            'complainant_email_content' => 1,
                            'complainee_email_content' => 1,
                            'complainee_department_email_content' => 1,
                            'complainant_department_email_content' => 1
                        ]
                    ]
                );

            return $latestEmail ?: [
                'complainant_email_content' => 'Default complainant email content',
                'complainee_email_content' => 'Default complainee email content',
                'complainee_department_email_content' => 'Default complainee department email content',
                'complainant_department_email_content' => 'Default complainant department email content'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch email content', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'complainant_email_content' => 'Default complainant email content',
                'complainee_email_content' => 'Default complainee email content',
                'complainee_department_email_content' => 'Default complainee department email content',
                'complainant_department_email_content' => 'Default complainant department email content'
            ];
        }
    }

    public function build()
    {
        $appointmentDate = $this->appointmentData['appointment_date'];
        if ($appointmentDate instanceof UTCDateTime) {
            $dateTime = $appointmentDate->toDateTime();
            $carbonDate = Carbon::instance($dateTime);
        } else {
            $carbonDate = Carbon::parse($appointmentDate);
        }
    
        $startTime = $this->appointmentData['appointment_start_time'];
        $endTime = $this->appointmentData['appointment_end_time'];
        
        $startDateTime = $carbonDate->copy()->setTimeFromTimeString($startTime);
        $endDateTime = $carbonDate->copy()->setTimeFromTimeString($endTime);
    
        $latestEmail = $this->getLatestEmailContent();
    
        $emailContent = match($this->recipientType) {
            true => $latestEmail['complainant_email_content'] ?? 'No content available',
            false => $latestEmail['complainee_email_content'] ?? 'No content available',
            'complainant_department' => $latestEmail['complainant_department_email_content'] ?? 'No department content available',
            'complainee_department' => $latestEmail['complainee_department_email_content'] ?? 'No department content available',
            default => 'No content available'
        };
    
        $recipientName = match($this->recipientType) {
            true, 'complainant_department' => $this->appointmentData['complainant_name'],
            false, 'complainee_department' => $this->appointmentData['respondent_name'],
            default => ''
        };
    
        return $this->subject('Notice of Invitation')
                    ->view('emails.appointment-notification')
                    ->with([
                        'appointmentDate' => $carbonDate->format('F d, Y'),
                        'appointmentStartTime' => $startDateTime->format('h:i A'),
                        'appointmentEndTime' => $endDateTime->format('h:i A'),
                        'emailContent' => $emailContent,
                        'recipientName' => $recipientName,
                    ]);
    }    
}