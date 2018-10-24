<?php

namespace App\Http\Controllers;

use App\Category;

class CategoryController extends Controller
{
    public function get()
    {
        return Category::all();
    }
}
