<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    protected $categories = [
        [
            'name'        => 'Nap',
            'icon'        => 'N',
            'description' => 'Nap spots are places on campus where you may rest your eyes.',
        ],
        [
            'name'        => 'Energy',
            'icon'        => 'E',
            'description' => 'Energy spots are places on campus where you can gain sustenance to fuel your day.',
            'crowdsource' => false,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->categories as $category) {
            $now = Carbon::now('America/New_York')->toDateTimeString();
            $timestamps = [
                'created_at' => $now,
                'updated_at' => $now,
            ];
            DB::table('categories')->insert(array_merge($category, $timestamps));
        }
    }
}
