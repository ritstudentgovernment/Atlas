<?php

namespace App\Listeners\Spots;

use App\Events\Spots\Created;
use App\Mail\SpotCreated;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class SendCreatedEmail
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
     * @param Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        if (!App::runningUnitTests()) {
            $spot = $event->spot;
            Mail::to($spot->author)->queue(new SpotCreated($spot));
        }
    }
}
