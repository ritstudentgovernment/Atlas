<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{

    public function index(){

        return User::all()->select('id','name','email')->get();

    }

    public function promoteReviewer($id){


        return User::findOrFail($id)->assignRole('reviewer');

    }

    public function promoteAdmin($id){

        return User::findOrFail($id)->assignRole('admin');

    }

}
