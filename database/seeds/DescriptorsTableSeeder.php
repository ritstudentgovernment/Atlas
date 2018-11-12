<?php

use App\Category;
use App\CategoryDescriptor;
use App\Descriptors as Descriptor;

class DescriptorsTableSeeder extends BaseSeeder
{

    protected function addDescriptorToCategories($map)
    {
        Category::all()->each(function(Category $category) use ($map) {
            if (array_key_exists($category->name, $map)) {
                $descriptors = $map[$category->name];
                $descriptors = is_array($descriptors) ?: [$descriptors];
                foreach ($descriptors as $descriptor) {
                    $descriptor = Descriptor::where('name', $descriptor)->first();
                    if ($descriptor) {
                        $categoryDescriptor = new CategoryDescriptor;
                        $categoryDescriptor->category_id = $category->id;
                        $categoryDescriptor->descriptor_id = $descriptor->id;
                        $categoryDescriptor->save();
                    }
                }
            }
        });
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->staticData = [
            [
                'name'              => 'Quiet Level',
                'default_value'     => 'Quiet',
                'allowed_values'    => 'Super Quiet|Quiet|Average|Noisy|Very Loud',
                'icon'              => 'rss',
            ],
            [
                'name'              => 'Fuel',
                'default_value'     => 'Coffee',
                'allowed_values'    => 'Coffee|Energy Drinks|Vending Machines|Food',
                'icon'              => 'bolt',
            ],
        ];

        $categoryDescriptorsMap = [
            'Energy'    => 'Fuel',
            'Nap'       => 'Quiet Level',
        ];

        $this->seed(Descriptor::class);
        $this->addDescriptorToCategories($categoryDescriptorsMap);
    }
}
