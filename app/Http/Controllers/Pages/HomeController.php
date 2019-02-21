<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Endpoint hit during homepage load.
     *
     * @return View the view to load.
     */
    public function index(Request $request)
    {
        $center_lat = (float) env('GOOGLE_MAPS_CENTER_LAT');
        $center_lng = (float) env('GOOGLE_MAPS_CENTER_LNG');
        $lat_range = (float) env('GOOGLE_MAPS_LAT_CHANGE');
        $lng_range = (float) env('GOOGLE_MAPS_LNG_CHANGE');

        if ($user = $request->user()) {
            $user->getRoleNames(); // add roles to the user object
        }

        return view('pages.home', [
            'map' => [
                'api_key' => env('GOOGLE_MAPS_API_KEY'),
                'center'  => [
                    'lat'     => $center_lat,
                    'lng'     => $center_lng,
                    'max_lat' => $center_lat + $lat_range,
                    'min_lat' => $center_lat - $lat_range,
                    'max_lng' => $center_lng + $lng_range,
                    'min_lng' => $center_lng - $lng_range,
                ],
            ],
            'user' => $user
        ]);
    }
}
