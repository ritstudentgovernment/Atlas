<?php

use App\Category;
use App\Descriptors as Descriptor;

class DescriptorsTableSeeder extends BaseSeeder
{
    protected function addDescriptorToCategories($map)
    {
        Category::all()->each(function (Category $category) use ($map) {
            if (array_key_exists($category->name, $map)) {
                $descriptors = $map[$category->name];
                $descriptors = is_array($descriptors) ? $descriptors : [$descriptors];
                foreach ($descriptors as $descriptor) {
                    $descriptor = Descriptor::where('name', $descriptor)->first();
                    if ($descriptor instanceof Descriptor) {
                        $descriptor->categories()->attach($category);
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
                'name'              => 'Sound Level',
                'default_value'     => 'Quiet',
                'allowed_values'    => 'Super Quiet|Quiet|Average|Noisy|Very Loud',
                'icon'              => 'rss',
            ],
            [
                'name'              => 'Comfort Level',
                'default_value'     => 'Comfortable',
                'allowed_values'    => 'Godly|Comfortable|Awkward|Hard|Not Comfortable',
                'icon'              => 'bed',
            ],
            [
                'name'              => 'Fuel',
                'default_value'     => 'Coffee',
                'allowed_values'    => 'Coffee|Energy Drinks|Food|Various',
                'icon'              => 'bolt',
            ],
        ];

        $categoryDescriptorsMap = [
            'Energy'    => 'Fuel',
            'Nap'       => ['Sound Level', 'Comfort Level'],
        ];

        $this->seed(Descriptor::class);
        $this->addDescriptorToCategories($categoryDescriptorsMap);
    }
}
