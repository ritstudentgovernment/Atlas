<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Collection;

class UserController extends Controller
{
    /**
     * Function to get all of the users of this application.
     *
     * @return Collection The collection of users.
     */
    public function index()
    {
        return User::where('id', '>', 0)->select('id', 'first_name', 'last_name', 'email')->get();
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
