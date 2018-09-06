<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpotCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {

        DB::table('spot_categories')->insert([

            'name' => "Nap",
            'description' => "A nap spot is a place on campus you can rest your eyes at.",
            'colorCode' => "#f46e22"

        ]);

        DB::table('spot_categories')->insert([

            'name' => "Energy",
            'description' => "An energy spot is a place on campus you can recharge.",
            'colorCode' => "#a04aff"

        ]);

    }
}
