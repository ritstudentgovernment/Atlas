<?php

namespace App\Http\Controllers;

use App\Descriptors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class DescriptorController extends Controller
{
    /**
     * Constructor to prevent unauthenticated access to sensitive routes.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role_or_permission:admin|administer');
    }

    public function list()
    {
        return Descriptors::all();
    }

    public function get($name)
    {
        return Descriptors::where('name', $name)->first();
    }

    public function delete(Request $request, Descriptors $descriptor)
    {
        if ($request->has('category_id')) {
            $descriptor->categories()->detach($request->input('category_id'));

            return response('Removed Descriptor Successfully', 200);
        } else {
            $descriptor->delete();

            return response('Deleted Descriptor Successful', 200);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'category_id'       => 'sometimes|required|integer',
            'name'              => 'required|string',
            'value_type'        => 'required|string',
            'default_value'     => 'required|string',
            'allowed_values'    => 'required|string',
            'icon'              => 'required|string',
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $descriptor = new Descriptors();
        $descriptor->name = $request->input('name');
        $descriptor->value_type = $request->input('value_type');
        $descriptor->default_value = $request->input('default_value');
        $descriptor->allowed_values = $request->input('allowed_values');
        $descriptor->icon = $request->input('icon');
        $descriptor->save();

        if ($request->has('category_id')) {
            $descriptor->categories()->attach($request->input('category_id'));
        }

        return $descriptor;
    }

    public function update(Request $request, Descriptors $descriptor)
    {
        $rules = [
            'category_id'       => 'sometimes|required|integer',
            'name'              => 'sometimes|required|string',
            'value_type'        => 'sometimes|required|string',
            'default_value'     => 'sometimes|required|string',
            'allowed_values'    => 'sometimes|required|string',
            'icon'              => 'sometimes|required|string',
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $updatedOne = false;
        foreach ($rules as $property => $rule) {
            if (Input::has($property)) {
                if ($property == 'category_id') {
                    $descriptor->categories()->attach($request->input('category_id'));
                } else {
                    $descriptor->$property = $request->input($property);
                    $descriptor->save();
                }
                $updatedOne = true;
            }
        }
        if (!$updatedOne) {
            return response('User did not supply any parameters that can be updated.', 400);
        }

        return response('Update Success', 200);
    }
}
