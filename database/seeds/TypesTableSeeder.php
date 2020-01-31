<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TypesTableSeeder extends Seeder
{
    protected $types = [
        [
            'name'        => 'Bench',
            'category_id' => 1,
        ],
        [
            'name'        => 'Couch',
            'category_id' => 1,
        ],
        [
            'name'        => 'Chair',
            'category_id' => 1,
        ],
        [
            'name'        => 'Cafe',
            'category_id' => 2,
        ],
        [
            'name'        => 'Vending Machine',
            'category_id' => 2,
        ],
        [
            'name'        => 'Academic',
            'category_id' => 3,
        ],
        [
            'name'        => 'Major Specific',
            'category_id' => 3,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->types as $type) {
            $now = Carbon::now('America/New_York')->toDateTimeString();
            $timestamps = [
                'created_at' => $now,
                'updated_at' => $now,
            ];
            DB::table('types')->insert(array_merge($type, $timestamps));
        }
    }
}
