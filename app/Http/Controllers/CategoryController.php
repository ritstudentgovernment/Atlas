<?php

namespace App\Http\Controllers;

use App\Category;
use App\Classification;
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
        return Category::with(['classifications', 'types'])->get();
    }

    public function store(Request $request)
    {
        $rules = [
            'name'          => 'required|string',
            'icon'          => 'required|string',
            'description'   => 'nullable|string',
        ];

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $category = new Category();
        $category->name = $request->input('name');
        $category->icon = $request->input('icon');
        $category->description = $request->input('description');
        $category->crowdsource = true;
        $category->active = false;
        $category->save();

        Classification::makeDefaultsForCategory($category);

        return $category;
    }

    public function update(Request $request, Category $category)
    {
        $rules = [
            'icon'          => 'sometimes|required|string',
            'active'        => 'sometimes|required|boolean',
            'crowdsource'   => 'sometimes|required|boolean',
            'description'   => 'sometimes|required|string',
        ];

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $updatedOne = false;
        foreach ($rules as $property => $rule) {
            if (Input::has($property)) {
                $category->$property = $request->input($property);
                $category->save();
                $updatedOne = true;
            }
        }
        if (!$updatedOne) {
            return response('User did not supply any parameters that can be updated.', 400);
        }

        return response('Update Success', 200);
    }

    /**
     * @param Request  $request
     * @param Category $category
     *
     * @throws \Exception
     */
    public function delete(Request $request, Category $category)
    {
        $category->spots()->delete();
        $category->types()->delete();
        $category->delete();
    }
}
