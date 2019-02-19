<?php

use App\Classification;
use App\Type;
use App\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Spot::class, function (Faker $faker) {
    $center_lat = (float) env('GOOGLE_MAPS_CENTER_LAT');
    $center_lng = (float) env('GOOGLE_MAPS_CENTER_LNG');
    $lat_range = (float) env('GOOGLE_MAPS_LAT_CHANGE');
    $lng_range = (float) env('GOOGLE_MAPS_LNG_CHANGE');

    $type = Type::first() ? Type::inRandomOrder()->first() : null;

    $classification = Classification::first() ?
        Classification::inRandomOrder()->get()->filter(function (Classification $classification) use ($type) {
            return $type ? $classification->category->id == $type->category->id : false;
        })->first() : null;

    return [

        'lat'       => $faker->randomFloat(5, $center_lat - $lat_range, $center_lat + $lat_range),
        'lng'       => $faker->randomFloat(5, $center_lng - $lng_range, $center_lng + $lng_range),

        'notes'     => $faker->text(100),
        'approved'  => $faker->boolean(),

        'user_id'           => User::first() ? User::inRandomOrder()->first()->id : null,
        'type_id'           => $type ? $type->id : null,
        'classification_id' => $classification ? $classification->id : null,

    ];
});
