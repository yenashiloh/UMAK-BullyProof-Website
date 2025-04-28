<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use Illuminate\Support\Facades\Log;

class AppointmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $appointmentData;
    public $recipientType;
    private $directEmailContent = null;
    private $emailContentCollection = null;

    /**
     * Create a new message instance.
     *
     * @param  array|object  $appointmentData
     * @param  mixed  $recipientTypeOrContent  
     *                true for complainant, false for complainee, 
     *                'complainant_department' or 'complainee_department' for departments
     *                or direct email content string
     * @return void
     */
    public function __construct($appointmentData, $recipientTypeOrContent = true)
    {
        $this->appointmentData = $appointmentData;
        
        // Check if the parameter is a recipient type identifier or direct content
        if (is_string($recipientTypeOrContent) && 
            !in_array($recipientTypeOrContent, ['complainant_department', 'complainee_department'])) {
            // It's direct email content
            $this->directEmailContent = $recipientTypeOrContent;
            $this->recipientType = true; // Default to complainant
        } else {
            // It's a recipient type indicator
            $this->recipientType = $recipientTypeOrContent;
            
            // Connect to MongoDB to fetch email templates later
            try {
                $client = new Client(env('MONGODB_URI'));
                $database = $client->selectDatabase(env('DB_DATABASE', 'bullyproof'));
                $this->emailContentCollection = $database->emailContent;
            } catch (\Exception $e) {
                Log::error('Failed to connect to MongoDB for email content', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    /**
     * Get the latest email content from the MongoDB collection
     *
     * @return array
     */
    private function getLatestEmailContent()
    {
        try {
            // If we have direct content, return that
            if ($this->directEmailContent !== null) {
                return [
                    'complainant_email_content' => $this->directEmailContent,
                    'complainee_email_content' => $this->directEmailContent,
                    'complainee_department_email_content' => $this->directEmailContent,
                    'complainant_department_email_content' => $this->directEmailContent
                ];
            }
            
            // Otherwise fetch from MongoDB
            if ($this->emailContentCollection) {
                $latestEmail = $this->emailContentCollection
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
            }
            
            // If we don't have a MongoDB connection, return defaults
            return [
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

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Handle appointment date based on its type
        $appointmentDate = $this->appointmentData['appointment_date'] ?? $this->appointmentData->appointment_date;
        if ($appointmentDate instanceof UTCDateTime) {
            $dateTime = $appointmentDate->toDateTime();
            $carbonDate = Carbon::instance($dateTime);
        } else {
            $carbonDate = Carbon::parse($appointmentDate);
        }
    
        // Get start and end times
        $startTime = $this->appointmentData['appointment_start_time'] ?? $this->appointmentData->appointment_start_time;
        $endTime = $this->appointmentData['appointment_end_time'] ?? $this->appointmentData->appointment_end_time;
        
        $startDateTime = $carbonDate->copy()->setTimeFromTimeString($startTime);
        $endDateTime = $carbonDate->copy()->setTimeFromTimeString($endTime);
    
        // Get email content based on recipient type
        $latestEmail = $this->getLatestEmailContent();
    
        $emailContent = match($this->recipientType) {
            true => $latestEmail['complainant_email_content'] ?? 'No content available',
            false => $latestEmail['complainee_email_content'] ?? 'No content available',
            'complainant_department' => $latestEmail['complainant_department_email_content'] ?? 'No department content available',
            'complainee_department' => $latestEmail['complainee_department_email_content'] ?? 'No department content available',
            default => 'No content available'
        };
    
        // Determine recipient name based on type
        $recipientName = $this->getRecipientName();
        
        // Replace placeholders in email content
        $emailContent = $this->replacePlaceholders($emailContent, $carbonDate, $startDateTime, $endDateTime);
    
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
    
    /**
     * Get the recipient name based on the recipient type
     *
     * @return string
     */
    private function getRecipientName()
    {
        $complainantName = is_array($this->appointmentData) 
            ? ($this->appointmentData['complainant_name'] ?? '') 
            : ($this->appointmentData->complainant_name ?? '');
            
        $respondentName = is_array($this->appointmentData) 
            ? ($this->appointmentData['respondent_name'] ?? '') 
            : ($this->appointmentData->respondent_name ?? '');
            
        return match($this->recipientType) {
            true, 'complainant_department' => $complainantName,
            false, 'complainee_department' => $respondentName,
            default => ''
        };
    }
    
    /**
     * Replace placeholders in email content
     *
     * @param string $content
     * @param Carbon $date
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @return string
     */
    private function replacePlaceholders($content, $date, $startTime, $endTime)
    {
        $complainantName = is_array($this->appointmentData) 
            ? ($this->appointmentData['complainant_name'] ?? '') 
            : ($this->appointmentData->complainant_name ?? '');
            
        $respondentName = is_array($this->appointmentData) 
            ? ($this->appointmentData['respondent_name'] ?? '') 
            : ($this->appointmentData->respondent_name ?? '');
            
        $placeholders = [
            '{complainant_name}' => $complainantName,
            '{respondent_name}' => $respondentName,
            '{appointment_date}' => $date->format('F d, Y'),
            '{appointment_start_time}' => $startTime->format('h:i A'),
            '{appointment_end_time}' => $endTime->format('h:i A'),
        ];
        
        return str_replace(array_keys($placeholders), array_values($placeholders), $content);
    }
}