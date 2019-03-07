<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:admin|administer');
    }

    public function index()
    {
        return view('pages.admin.dashboard');
    }
}
