<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Spot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SpotController extends Controller
{

    /**
     * Constructor to prevent unauthenticated access to sensitive routes.
     */
    public function __construct(){

        $this->middleware('auth')->except(["index","get"]);

    }

    /**
     * Function to get the nap spots the current user may see.
     *
     * @param Request $request The Http request.
     * @return Collection The collection of spots a user is supposed to see.
     */
    public function get(Request $request){

        if($user = auth()->user()){

            // Get spots a user is authorized to see.
            if(!$user->hasPermissionTo('view unapproved spots')){

                // The user is logged in but they do not have permission to view unapproved spots
                return Spot::where("status","!=",0)->orWhere("user_id","=",$user->id)->get();

            }

            // The user is logged in and they have permission to view unapproved spots
            return Spot::all();

        }
        else{

            // The user is not logged in
            return Spot::where("status","!=",0)->get();

        }

    }

    /**
     * Endpoint hit during homepage load.
     *
     * @param Request $request The HTTP request.
     * @return View the view to load.
     */
    public function index(Request $request){

        $center_lat = (float)env("GOOGLE_MAPS_CENTER_LAT");
        $center_lng = (float)env("GOOGLE_MAPS_CENTER_LNG");
        $lat_range  = (float)env("GOOGLE_MAPS_LAT_CHANGE");
        $lng_range  = (float)env("GOOGLE_MAPS_LNG_CHANGE");

        return view('index', [

            "map" => [
                "api_key" => env("GOOGLE_MAPS_API_KEY"),
                "center"  => [
                    "lat" => $center_lat,
                    "lng" => $center_lng,
                    "max_lat" => $center_lat + $lat_range,
                    "min_lat" => $center_lat + $lat_range,
                    "max_lng" => $center_lng + $lng_range,
                    "mix_lng" => $center_lng + $lng_range
                ]
            ],
            "spots" => $this->get($request)

        ]);

    }

    /**
     * Endpoint hit for creating nap spots.
     *
     * @param Request $request The HTTP request.
     * @return Spot The new spot.
     */
    public function store(Request $request){

        $rules = array(
            'title'     => 'required',
            'quietLevel'=> 'required|numeric',
            'notes'     => 'required',
            'type_id'   => 'required|numeric',
            'lat'       => 'required|numeric',
            'lng'       => 'required|numeric',
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) return $validator->errors();

        $spot = Spot::create([

            'title'     => $request->input('title'),
            'quietLevel'=> $request->input('quietLevel'),
            'notes'     => $request->input('notes'),
            'type_id'   => $request->input('type_id'),
            'lat'       => $request->input('lat'),
            'lng'       => $request->input('lng'),

        ]);

        return $spot;

    }

}
