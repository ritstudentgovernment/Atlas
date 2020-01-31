<?php

use App\Category;
use App\Descriptors;

class DescriptorsTableSeeder extends BaseSeeder
{
    protected function addDescriptorToCategories($map)
    {
        Category::all()->each(function (Category $category) use ($map) {
            if (array_key_exists($category->name, $map)) {
                $descriptors = $map[$category->name];
                $descriptors = is_array($descriptors) ? $descriptors : [$descriptors];
                foreach ($descriptors as $descriptor) {
                    $descriptor = Descriptors::where('name', $descriptor)->first();
                    if ($descriptor instanceof Descriptors) {
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
                'icon'              => 'rss',
                'value_type'        => 'select',
                'default_value'     => 'Quiet',
                'allowed_values'    => 'Super Quiet|Quiet|Average|Noisy|Very Loud',
            ],
            [
                'name'              => 'Comfort Level',
                'icon'              => 'happy',
                'value_type'        => 'select',
                'default_value'     => 'Comfortable',
                'allowed_values'    => 'Godly|Comfortable|Awkward|Hard|Not Comfortable',
            ],
            [
                'name'              => 'Fuel',
                'icon'              => 'bolt',
                'value_type'        => 'select',
                'default_value'     => 'Coffee',
                'allowed_values'    => 'Coffee|Energy Drinks|Food|Various',
            ],
            [
                'name'              => 'Building',
                'icon'              => 'location',
                'value_type'        => 'select',
                'default_value'     => 'George Eastman Hall',
                'allowed_values'    => 'Brown Hall|Center for Bioscience Education & Technology|Center for Student Innovation|Chester F. Carlson Center for Imaging Science|Color Science Hall|Engineering Hall|Engineering Technology Hall (LEED Gold)|Frank E. Gannett Hall|George Eastman Hall|Golisano Hall|Hugh L. Carey Hall|Institute Hall (LEED Gold)|James E. Booth Hall|James E. Gleason Hall|Laboratory for Applied Computing|Lewis P. Ross Hall|Liberal Arts Hall|Louise Slaughter Hall|Lyndon Baines Johnson Hall|Max Lowenthal Hall|Orange Hall|Sands Family Studios|Sustainability Institute Hall (LEED Platinum)|The Wallace Center|Thomas Gosnell Hall|University Gallery|University Services Center (LEED Platinum)|Vignelli Center for Design Studies|Professional Office Annex|MAGIC Spell Studios|Rosica Hall|Student Development Center',
            ],
            [
                'name'              => 'Floor',
                'icon'              => 'info',
                'value_type'        => 'number',
                'default_value'     => '1',
                'allowed_values'    => '0-10',
            ],
            [
                'name'              => 'Size',
                'icon'              => 'album',
                'value_type'        => 'number',
                'default_value'     => '1',
                'allowed_values'    => '1-20',
            ],
            [
                'name'              => 'Available Computers',
                'icon'              => 'desktop',
                'value_type'        => 'multiSelect',
                'default_value'     => 'iMacs',
                'allowed_values'    => 'iMacs|PCs|Linux',
            ],
        ];

        $categoryDescriptorsMap = [
            'Energy'    => ['Fuel', 'Building', 'Floor'],
            'Nap'       => ['Sound Level', 'Comfort Level', 'Building', 'Floor', 'Size'],
            'Lab'       => ['Available Computers', 'Sound Level', 'Building', 'Floor'],
        ];

        $this->seed(Descriptors::class);
        $this->addDescriptorToCategories($categoryDescriptorsMap);
    }
}
