<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class SAMLController extends Controller
{
    public function login()
    {
        $guestRedirect = config('saml2_settings.routesPrefix').'/login';
        $authenticatedRedirect = config('saml2_settings.loginRoute');
        return Auth::guest() ? redirect($guestRedirect) : Redirect::intended($authenticatedRedirect);
    }
    public function logout()
    {
        Auth::logout();
        Session::save();
        return Redirect::intended(config('saml2_settings.logoutRoute'));
    }
    public function loggedin()
    {
        return view('home');
    }
}
