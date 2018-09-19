<?php

namespace App\Http\Controllers;

use App\Category;

class SpotCategoryController extends Controller
{

    public function get(){

        return Category::all();

    }

}
