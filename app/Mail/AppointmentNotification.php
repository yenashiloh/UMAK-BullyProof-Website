<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use MongoDB\BSON\UTCDateTime;


class AppointmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $appointmentData;

    public function __construct($appointmentData)
    {
        $this->appointmentData = $appointmentData;
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

        return $this->subject('Notice of Invitation')
                    ->view('emails.appointment-notification')
                    ->with([
                        'appointmentDate' => $carbonDate->format('F d, Y'),
                        'appointmentStartTime' => $startDateTime->format('h:i A'),
                        'appointmentEndTime' => $endDateTime->format('h:i A'),
                    ]);
    }
}