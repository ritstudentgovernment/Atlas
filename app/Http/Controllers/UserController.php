<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
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
    public function all()
    {
        return User::allUsers()->get();
    }

    /**
     * Function to get all of the staff users of this application.
     *
     * @return Collection The collection of users.
     */
    public function staff()
    {
        return User::staff();
    }

    /**
     * Function to get a specific users data.
     *
     * @param Request $request
     * @param User    $user
     *
     * @return User The collection of users.
     */
    public function get(Request $request, User $user)
    {
        // Add roles to user response
        $user->hasAnyRole(['admin', 'reviewer']);

        return $user;
    }

    /**
     * Function to promote a user to a reviewer role.
     *
     * @param User $user
     *
     * @return User
     */
    public function promoteReviewer(User $user)
    {
        return $user->assignRole('reviewer');
    }

    /**
     * Function to promote a user to the admin role.
     *
     * @param User $user
     *
     * @return User
     */
    public function promoteAdmin(User $user)
    {
        if ($user->hasRole('reviewer')) {
            $user->removeRole('reviewer');
        }
        return $user->assignRole('admin');
    }

    /**
     * Function to demote a user from the reviewer role.
     *
     * @param User $user
     *
     * @return User
     */
    public function demoteReviewer(User $user)
    {
        $user->removeRole('reviewer');
        return $user;
    }

    /**
     * Function to demote a user from the admin role.
     *
     * @param User $user
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response | User
     */
    public function demoteAdmin(Request $request, User $user)
    {
        if ($request->user('api')->id == $user->id) {
            return response('You cannot remove yourself from the admin role', 400);
        }
        $user->removeRole('admin');

        return $user;
    }
}
