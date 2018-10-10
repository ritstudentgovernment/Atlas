<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Do not seed the users table if the App is set to production mode.
        if(env("APP_ENV") == 'local') {

            $now = Carbon::now('America/New_York')->toDateTimeString();
            $users = [
                'Admin', 'Staff', 'User'
            ];
            foreach ($users as $user) {
                DB::table('users')->insert([

                    'first_name'=> "$user",
                    'last_name' => "User",
                    'email'     => strtolower($user)."@rit.edu",
                    'password'  => "shibboleth",
                    'created_at'=> $now,
                    'updated_at'=> $now

                ]);
            }
        }
    }
}
