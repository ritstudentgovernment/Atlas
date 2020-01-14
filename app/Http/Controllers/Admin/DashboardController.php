<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    private $pageLinks;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:admin|administer');

        // Compute an array of all of the links to sub pages in the admin panel.
        $allRoutes = collect(Route::getroutes()->get());
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

    public function categories()
    {
        return view('pages.admin.categories.categories', [
            'pageLinks'  => json_encode($this->pageLinks),
            'categories' => json_encode(Category::with(['types', 'classifications', 'descriptors'])->get()),
        ]);
    }

    public function category(Category $category)
    {
        $category->descriptors;
        $category->types;
        $category->classifications;
        $category->setHidden([]);

        return view('pages.admin.categories.category', [
            'category'  => $category,
            'pageLinks' => json_encode($this->pageLinks),
        ]);
    }

    public function users()
    {
        return view('pages.admin.users.users', [
            'pageLinks'  => json_encode($this->pageLinks),
            'users'      => json_encode(User::allUsers()->get()),
        ]);
    }

    public function staff()
    {
        return view('pages.admin.users.staff', [
            'pageLinks'  => json_encode($this->pageLinks),
            'users'      => json_encode(User::staff()),
        ]);
    }

    public function settings()
    {
        return view('pages.admin.settings', [
            'pageLinks'  => json_encode($this->pageLinks),
        ]);
    }
}
