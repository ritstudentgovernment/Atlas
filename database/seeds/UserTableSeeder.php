<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        if (env('APP_ENV') == 'local') {
            $now = Carbon::now('America/New_York')->toDateTimeString();
            $users = [
                'Cooper'  => 'Sheldon',
                'Sanchez' => 'Rick',
                'Smith'   => 'Morty',
            ];
            foreach ($users as $last => $first) {
                DB::table('users')->insert([

                    'first_name'=> $first,
                    'last_name' => $last,
                    'email'     => substr(strtolower($first), 0, 1).strtolower($last).'@samltest.id',
                    'password'  => bcrypt(str_random(8)),
                    'created_at'=> $now,
                    'updated_at'=> $now,

                ]);
            }
        }
    }
}
