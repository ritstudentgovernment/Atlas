<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Http\Parser\Cookies;

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
        Session::flush();

        return Redirect::intended(config('saml2_settings.logoutRoute'));
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
        ]);
    }
}
