<?php

namespace App\Http\Controllers;

use App\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class TypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role_or_permission:admin|administer');
    }

    public function delete(Request $request, Type $type)
    {
        $type->deleted = true;
        $type->save();
    }

    public function store(Request $request)
    {
        $rules = [
            'name'      => 'required|string',
            'category'  => 'required|numeric',
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $type = new Type();
        $type->name = $request->input('name');
        $type->category_id = $request->input('category');
        $type->save();

        return $type;
    }
}
