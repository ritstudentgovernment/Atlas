<?php

namespace App\Http\Controllers;

use App\Classification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClassificationController extends Controller
{
    /**
     * Constructor to prevent unauthenticated access to sensitive routes.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role_or_permission:admin|administer');
    }

    public function softDelete(Request $request, Classification $classification)
    {
        $classification->deleted = true;
        $classification->save();
    }

    public function delete(Request $request, Classification $classification)
    {
        try {
            $classification->spots()->delete();
            $classification->delete();
            return response("Deletion successful", 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response("Error deleting Classification",500);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'category_id'       => 'required|integer',
            'create_permission' => 'nullable|string',
            'view_permission'   => 'nullable|string',
            'color'             => 'required|string',
            'name'              => 'required|string'
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $classification = new Classification();
        $classification->name = $request->input('name');
        $classification->color = $request->input('color');
        $classification->view_permission = $request->input('view_permission');
        $classification->create_permission = $request->input('create_permission');
        $classification->category_id = $request->input('category_id');
        $classification->save();

        return $classification;
    }

    public function update(Request $request, Classification $classification)
    {
        $rules = [
            'category_id'       => 'sometimes|required|integer',
            'create_permission' => 'sometimes|nullable|string',
            'view_permission'   => 'sometimes|nullable|string',
            'color'             => 'sometimes|required|string',
            'name'              => 'sometimes|required|string'
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $updatedOne = false;
        foreach ($rules as $property => $rule){
            if (Input::has($property)) {
                $classification->$property = $request->input($property);
                $classification->save();
                $updatedOne = true;
            }
        }
        if (!$updatedOne) {
            return response("User did not supply any parameters that can be updated.", 400);
        }
        return response("Update Success", 200);
    }

}
