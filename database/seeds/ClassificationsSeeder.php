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
                'name'            => 'Under Review',
                'color'           => 'cb0020',
                'category_id'     => 1,
                'view_permission' => 'view unapproved spots',
            ],
            [
                'name'        => 'Public',
                'color'       => '298237',
                'category_id' => 2,
                'create_permission' => 'approve spots',
            ],
            [
                'name'              => 'Designated',
                'color'             => '40ca56',
                'category_id'       => 2,
                'create_permission' => 'make designated spots',
            ],
        ];

        $this->seed(Classification::class);
    }
}
