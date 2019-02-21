<?php

use App\Classification;

class ClassificationsSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->staticData = [
            [
                'name'        => 'Public',
                'color'       => '8d5632',
                'category_id' => 1,
            ],
            [
                'name'              => 'Designated',
                'color'             => 'ff7700',
                'category_id'       => 1,
                'create_permission' => 'make designated spots',
            ],
            [
                'name'              => 'Under Review',
                'color'             => 'cb0020',
                'category_id'       => 1,
                'view_permission'   => 'view unapproved spots',
                'create_permission' => 'create under review spots',
            ],
            [
                'name'              => 'Public',
                'color'             => '298237',
                'category_id'       => 2,
            ],
            [
                'name'              => 'Designated',
                'color'             => '40ca56',
                'category_id'       => 2,
                'create_permission' => 'make designated spots',
            ],
            [
                'name'              => 'Under Review',
                'color'             => 'cb0020',
                'category_id'       => 2,
                'view_permission'   => 'view unapproved spots',
                'create_permission' => 'create under review spots',
            ],
        ];

        $this->seed(Classification::class);
    }
}
