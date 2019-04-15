<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Collection;

class UserController extends Controller
{

    /**
     * Constructor to prevent unauthenticated access to sensitive routes.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role_or_permission:admin|administer');
    }

    /**
     * Function to get all of the users of this application.
     *
     * @return Collection The collection of users.
     */
    public function get()
    {
        return User::allUsers()->get();
    }

    /**
     * Function to promote a user to a reviewer role.
     */
    public function promoteReviewer(User $user)
    {
        return $user->assignRole('reviewer');
    }

    public function promoteAdmin(User $user)
    {
        if (!$user->hasRole('reviewer')) {
            $this->promoteReviewer($user);
        }

        return $user->assignRole('admin');
    }
}
