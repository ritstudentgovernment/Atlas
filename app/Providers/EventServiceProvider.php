<?php

namespace App\Providers;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use App\Listeners\Saml2LoginEventListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Saml2LoginEvent::class => [
            Saml2LoginEventListener::class,
        ],
        'App\Events\Spots\Created' => [
            'App\Listeners\Spots\SendCreatedEmail'
        ],
        'App\Events\Spots\Approved' => [
            'App\Listeners\Spots\SendApprovedEmail'
        ],
        'App\Events\Spots\Rejected' => [
            'App\Listeners\Spots\SendRejectedEmail'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
