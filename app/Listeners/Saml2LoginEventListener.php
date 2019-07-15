<?php

namespace App\Listeners;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Aacotroneo\Saml2\Saml2User;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class Saml2LoginEventListener
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

    private function getUserAttributes(Saml2User $user, $attributeMap)
    {
        $user->parseAttributes($attributeMap);

        $attributes = [];
        foreach ($attributeMap as $attribute => $samlKey) {
            if ($user->{$attribute}) {
                $attributes[$attribute] = $user->{$attribute}[0];
            } else {
                \Log::debug("Attribute $attribute not found in user object.");
            }
        }

        if (env('APP_DEBUG')) {
            \Log::debug($attributes);
        }

        return $attributes;
    }

    /**
     * Handle the event.
     *
     * @param Saml2LoginEvent $event
     *
     * @return void
     */
    public function handle(Saml2LoginEvent $event)
    {
        $user = $event->getSaml2User();

        $attributeMap = [
            'EmailAddress'=> 'urn:oid:0.9.2342.19200300.100.1.3',
            'FirstName'   => 'urn:oid:2.5.4.42',
            'LastName'    => 'urn:oid:2.5.4.4',
        ];

        $userAttributes = $this->getUserAttributes($user, $attributeMap);

        //check if email already exists and fetch user
        $user = User::where('email', $userAttributes['EmailAddress'])->first();

        //if email doesn't exist, create new user
        if ($user === null) {
            $user = new User();
            $user->first_name = $userAttributes['FirstName'];
            $user->last_name = $userAttributes['LastName'];
            $user->email = $userAttributes['EmailAddress'];
            $user->password = bcrypt(str_random(8));
            $user->save();
        }

        //login user
        session(['api_key' => JWTAuth::fromUser($user)]);
        Auth::login($user);
    }
}
