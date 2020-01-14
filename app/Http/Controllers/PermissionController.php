<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Constructor to prevent unauthenticated access to sensitive routes.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role_or_permission:admin|administer');
    }

    public function all(Request $request)
    {
        return Permission::all();
    }
}
