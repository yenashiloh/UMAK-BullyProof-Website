<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

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
        return $this->subject('Notice of Invitation')
                    ->view('emails.appointment-notification');
    }
}