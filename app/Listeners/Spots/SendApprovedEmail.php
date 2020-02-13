<?php

namespace App\Listeners\Spots;

use App\Events\Spots\Approved;
use App\Mail\SpotApproved;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class SendApprovedEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param Approved $event
     *
     * @return void
     */
    public function handle(Approved $event)
    {
        if (!App::runningUnitTests()) {
            $spot = $event->spot;
            Mail::to($spot->author)->send(new SpotApproved($spot));
        }
    }
}
