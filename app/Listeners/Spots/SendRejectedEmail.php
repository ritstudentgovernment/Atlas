<?php

namespace App\Listeners\Spots;

use App\Events\Spots\Rejected;
use App\Mail\SpotRejected;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class SendRejectedEmail
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
     * @param  Rejected  $event
     * @return void
     */
    public function handle(Rejected $event)
    {
        if (!App::runningUnitTests()) {
            $spot = $event->spot;
            Mail::to($spot->author)->queue(new SpotRejected($spot));
        }
    }
}
