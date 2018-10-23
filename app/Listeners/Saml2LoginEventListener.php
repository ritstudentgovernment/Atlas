<?php

namespace App\Listeners;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Aacotroneo\Saml2\Saml2User;
use App\User;
use Illuminate\Support\Facades\Auth;

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

    private function getUserProperties(Saml2User $user, $properties){

        $user->parseAttributes($properties);

        $attributes = [];
        foreach($properties as $property => $samlKey){

            $flattened_property_value = array_flatten($user->{$property})[0];
            $attributes[$property] = $flattened_property_value;

        }

        return $attributes;

    }

    /**
     * Handle the event.
     *
     * @param  Saml2LoginEvent  $event
     * @return void
     */
    public function handle(Saml2LoginEvent $event)
    {
        $user = $event->getSaml2User();

        $propertyMap = [

            "EmailAddress"=>"urn:oid:0.9.2342.19200300.100.1.3",
            "FirstName"=>"urn:oid:2.5.4.42",
            "LastName"=>"urn:oid:2.5.4.4",

        ];

        $userData = [
            'id' => $user->getUserId(),
            'attributes' => $this->getUserProperties($user, $propertyMap),
            'assertion' => $user->getRawSamlAssertion(),
            'sessionIndex' => $user->getSessionIndex(),
            'nameId' => $user->getNameId()
        ];

        \Log::debug($userData['attributes']);
        //check if email already exists and fetch user
        $user = User::where('email', $userData['attributes']['EmailAddress'])->first();

        //if email doesn't exist, create new user
        if($user === null)
        {
            $user = new User;
            $user->first_name = $userData['attributes']['FirstName'];
            $user->last_name = $userData['attributes']['LastName'];
            $user->email = $userData['attributes']['EmailAddress'];
            $user->password = bcrypt(str_random(8));
            $user->save();
        }

        //insert sessionIndex and nameId into session
        session(['sessionIndex' => $userData['sessionIndex']]);
        session(['nameId' => $userData['nameId']]);
        //login user
        Auth::login($user);
    }
}
