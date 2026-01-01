<?php

namespace App\Mail;

use App\Models\StaffLeave;
use App\Models\Staff;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestRejected extends Mailable implements ShouldQueue
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
    public $rejector;
    public $reason;

    /**
     * Create a new message instance.
     *
     * @param StaffLeave $leave
     * @param Staff $applicant
     * @param Staff $rejector
     * @param string $reason
     * @return void
     */
    public function __construct(StaffLeave $leave, Staff $applicant, Staff $rejector, $reason = '')
    {
        $this->leave = $leave;
        $this->applicant = $applicant;
        $this->rejector = $rejector;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Leave Request Has Been Rejected')
            ->view('emails.leave-rejected');
    }
}
