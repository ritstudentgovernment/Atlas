<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Spot;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class SpotController extends Controller
{

    public function __construct(){

//        $this->middleware('auth')->except("guest");

    }

    public function guest(Request $request){

        return Spot::where('status', 1)->get();

    }

    public function store(Request $request){

        /**
         *
         * Endpoint hit for creating nap spots.
         *
         * @param: Request; the HTTP request.
         *
         * @returns:
         *
         **/

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
