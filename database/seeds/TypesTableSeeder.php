<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $types = [
            [
                'name' => 'Bench',
                'category_id' => 1
            ],
            [
                'name' => 'Couch',
                'category_id' => 1
            ],
            [
                'name' => 'Chair',
                'category_id' => 1
            ],
            [
                'name' => 'Coffee Shop',
                'category_id' => 2
            ],
            [
                'name' => 'Vending Machine',
                'category_id' => 2
            ]
        ];
        foreach ($types as $type) {
            $now = Carbon::now('America/New_York')->toDateTimeString();
            $timestamps = [
                'created_at' => $now,
                'updated_at' => $now
            ];
            DB::table('types')->insert(array_merge($type, $timestamps));
        }

    }
}
