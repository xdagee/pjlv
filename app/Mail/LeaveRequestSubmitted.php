<?php

namespace App\Mail;

use App\Models\StaffLeave;
use App\Models\Staff;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public int $backoff = 60;

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
