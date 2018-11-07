<?php

namespace App\Http\Controllers;

use App\Spot;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class SpotController extends Controller
{
    /**
     * Constructor to prevent unauthenticated access to sensitive routes.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except(['get']);
    }

    /**
     * Function to get the nap spots the current user may see.
     *
     * @param Request $request The Http request.
     *
     * @return Collection The collection of spots a user is supposed to see.
     */
    public function get(Request $request)
    {
        if (($user = $request->user()) && $user instanceof User) {

            // Get spots a user is authorized to see.
            if (!$user->can('view unapproved spots')) {
                // The user is logged in but they do not have permission to view unapproved spots
                return Spot::where('approved', 1)->orWhere('user_id', $user->id)->get();
            }

            // The user is logged in and they have permission to view unapproved spots
            return Spot::all();
        } else {
            // The user is not logged in
            return Spot::where('approved', 1)->get();
        }
    }

    /**
     * Endpoint hit for creating nap spots.
     *
     * @param Request $request The HTTP request.
     *
     * @return Spot The new spot.
     */
    public function store(Request $request)
    {
        $rules = [
            'title'     => 'required',
            'notes'     => 'required',
            'building'  => 'required',
            'floor'     => 'required|numeric',
            'type_id'   => 'required|numeric',
            'lat'       => 'required|numeric',
            'lng'       => 'required|numeric',
        ];

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $spot = Spot::create([

            'title'     => $request->input('title'),
            'notes'     => $request->input('notes'),
            'building'  => $request->input('building'),
            'floor'     => $request->input('floor'),
            'type_id'   => $request->input('type_id'),
            'lat'       => $request->input('lat'),
            'lng'       => $request->input('lng'),
            'approved'  => $request->user()->can('approve spots') ? 1 : 0,
            'user_id'   => $request->user()->id,

        ]);

        return $spot;
    }

    /**
     * Endpoint hit to approve a nap spot.
     *
     * @param Request $request the http request.
     * @param Spot    $spot    the spot to try and approve
     *
     * @return void
     */
    public function approve(Request $request, Spot $spot)
    {
        $spot->approved = true;
        $spot->save();
    }
}
