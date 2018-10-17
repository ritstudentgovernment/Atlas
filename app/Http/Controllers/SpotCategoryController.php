<?php

namespace App\Http\Controllers;

use App\SpotCategory;

class SpotCategoryController extends Controller
{
    public function get()
    {
        return SpotCategory::all();
    }
}
