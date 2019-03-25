<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Type;

class DashboardController extends Controller
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
            if (array_key_exists('as', $route->action)) {
                $page = $route->action['as'];
                $this->pageLinks[$page] = "/$route->uri";
            }
        });
    }

    public function index()
    {
        return view('pages.admin.dashboard', [
            'pageLinks' => json_encode($this->pageLinks),
        ]);
    }

    public function spotCategories()
    {
        return view('pages.admin.spots', [
            'pageLinks' => json_encode($this->pageLinks),
            'categories' => json_encode(Category::with(['types', 'classifications', 'descriptors'])->get())
        ]);
    }

    public function showCategory(Category $category)
    {
        return view('pages.admin.category', [
            'category' => $category,
            'pageLinks' => json_encode($this->pageLinks)
        ]);
    }
}
