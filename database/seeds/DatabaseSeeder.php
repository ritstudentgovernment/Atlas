<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([

            UserTableSeeder::class,
            CategoriesTableSeeder::class,
            TypesTableSeeder::class,
            DescriptorsTableSeeder::class,
            SpotsTableSeeder::class,
            PermissionsSeeder::class,

        ]);
    }
}
