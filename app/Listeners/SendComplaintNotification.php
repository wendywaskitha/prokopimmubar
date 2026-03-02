<?php

namespace App\Listeners;

use App\Events\ComplaintSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComplaintSubmittedMail;

class SendComplaintNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ComplaintSubmitted $event): void
    {
        // Kirim email notifikasi ke pelapor
        Mail::to($event->complaint->email)->send(new ComplaintSubmittedMail($event->complaint));
    }
}
