<?php

namespace App\Http\Controllers;

use App\Category;
use App\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Constructor to prevent unauthenticated access to sensitive routes.
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except(['get']);
        $this->middleware('role_or_permission:admin|administer')->except(['get']);
    }

    public function get()
    {
        return Category::all();
    }

    public function storeType(Request $request)
    {
        $rules = [
            'category'          => 'required|numeric',
            'type_name'         => 'required|string',
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }
        $type = new Type();
        $type->name = $request->input('type_name');
        $type->category_id = $request->input('category');
        $type->save();

        return $type;
    }
}
