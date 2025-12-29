<?php

namespace App\Mail;

use App\StaffLeave;
use App\Staff;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    public $applicant;

    /**
     * Create a new message instance.
     *
     * @param StaffLeave $leave
     * @param Staff $applicant
     * @return void
     */
    public function __construct(StaffLeave $leave, Staff $applicant)
    {
        $this->leave = $leave;
        $this->applicant = $applicant;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Leave Request from ' . $this->applicant->firstname . ' ' . $this->applicant->lastname)
            ->view('emails.leave-submitted');
    }
}
