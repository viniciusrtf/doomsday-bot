<?php

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

$factory->define(App\Models\NearEarthObjects\NearEarthObject::class, function (Faker $faker) {
    static $hazardous;
    static $approachDate;

    return [
        'provider_id' => rand(0,9999), 
        'name' => $faker->name, 
        'info_url' => $faker->url, 
        'hazardous' => $hazardous ?: 1, 
        'estimated_diameter' => 99999,
        'relative_velocity' => 99999, 
        'mass_distance' => 99999, 
        'approach_date' => $approachDate ?: $faker->date()
    ];
});
