<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    private $pageLinks;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:admin|administer');

        // Compute an array of all of the links to sub pages in the admin panel.
        $allRoutes = collect(\Illuminate\Support\Facades\Route::getroutes()->get());
        $allRoutes->filter(function (\Illuminate\Routing\Route $route) {
            return strpos($route->uri, 'admin') === 0;
        })->each(function (\Illuminate\Routing\Route $route) {
            if (key_exists('as', $route->action)) {
                $page = $route->action['as'];
                $prefix = ltrim(str_replace('/', '.', $route->action['prefix']), '.');
                $this->pageLinks["$prefix.$page"] = "/$route->uri";
            }
        });
    }

    public function index()
    {
        return view('pages.admin.dashboard', [
            'pageLinks' => json_encode($this->pageLinks)
        ]);
    }

    public function manageTypes()
    {
        return view('pages.admin.spots.types', [
            'pageLinks' => json_encode($this->pageLinks)
        ]);
    }
}
