<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpotTypeTableSeeder extends Seeder
{
    private function makeSpotType($name, $category)
    {
        DB::table('spot_types')->insert([

            'name'        => $name,
            'category_id' => $category,

        ]);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->makeSpotType('Chair', 1);
        $this->makeSpotType('Couch', 1);
        $this->makeSpotType('Bench', 1);

        $this->makeSpotType('Vending Machine', 2);
        $this->makeSpotType('Coffee', 2);
    }
}
