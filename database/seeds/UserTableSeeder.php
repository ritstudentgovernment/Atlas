<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    protected $users = [
        'Cooper'  => 'Sheldon',
        'Sanchez' => 'Rick',
        'Smith'   => 'Morty',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now('America/New_York')->toDateTimeString();
        if (env('APP_ENV') == 'local' || env('APP_ENV') == 'testing') {
            // Do not seed the users table if the App is set to production mode.
            foreach ($this->users as $last => $first) {
                DB::table('users')->insert([

                    'first_name' => $first,
                    'last_name'  => $last,
                    'email'      => substr(strtolower($first), 0, 1).strtolower($last).'@samltest.id',
                    'password'   => bcrypt(str_random(8)),
                    'created_at' => $now,
                    'updated_at' => $now,

                ]);
            }
        } elseif (env('DEFAULT_ADMIN_FN') && env('DEFAULT_ADMIN_LN') && env('DEFAULT_ADMIN_EM')) {
            DB::table('users')->insert([

                'first_name' => env('DEFAULT_ADMIN_FN'),
                'last_name'  => env('DEFAULT_ADMIN_LN'),
                'email'      => env('DEFAULT_ADMIN_EM'),
                'password'   => bcrypt(str_random(8)),
                'created_at' => $now,
                'updated_at' => $now,

            ]);
        }
    }
}
