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

            'name' => "Nap Spot",
            'icon_prefix' => "naps",
            'description' => "A nap spot is a place on campus you can rest your eyes at."

        ]);

        DB::table('spot_categories')->insert([

            'name' => "Energy Spot",
            'icon_prefix' => "energy",
            'description' => "An energy spot is a place on campus you can recharge."

        ]);

    }
}
