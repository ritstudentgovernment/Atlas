<?php

use Illuminate\Database\Seeder;

use App\Spot;
use App\Descriptors;
use App\DescriptorSpot;
use \Illuminate\Support\Collection;

class SpotsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Spot::class, 50)->create()->each(function (Spot $spot) {
            $descriptors = $spot->type->category->descriptors;
            if ($descriptors instanceof Collection) {
                $descriptors->each(function (Descriptors $descriptor) use ($spot) {
                    $spot->descriptors()->sync([$descriptor->id => ['value'=>$descriptor->default_value]]);
                });
            } else {
                \Log::error("Descriptors should be a collection instance");
            }
        });
    }
}
