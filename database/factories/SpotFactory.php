<?php

use App\SpotType as Type;
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
    $types = Type::all();
    $center_lat = (float) env('GOOGLE_MAPS_CENTER_LAT');
    $center_lng = (float) env('GOOGLE_MAPS_CENTER_LNG');
    $lat_range = (float) env('GOOGLE_MAPS_LAT_CHANGE');
    $lng_range = (float) env('GOOGLE_MAPS_LNG_CHANGE');

    return [
        'title'      => $faker->name,
        'quietLevel' => $faker->numberBetween(0, 3),
        'notes'      => $faker->text(100),
        'status'     => $faker->numberBetween(0, 2),
        'type_id'    => $faker->numberBetween(1, count($types)),
        'user_id'    => User::inRandomOrder()->first()->id,
        'lat'        => $faker->randomFloat(5, $center_lat - $lat_range, $center_lat + $lat_range),
        'lng'        => $faker->randomFloat(5, $center_lng - $lng_range, $center_lng + $lng_range),
    ];
});
