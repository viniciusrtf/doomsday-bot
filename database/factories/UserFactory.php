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

$factory->define(App\User::class, function (Faker $faker) {
    static $password;
    static $isNeoNotificationEnabled;
    static $neoNotificationChannel;
    static $neoNotificationDaysInAdvance;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        "telegram_id" => rand(0,9999999999999),
        "info" => $faker->realText(200),
        "is_neo_notification_enabled" => $isNeoNotificationEnabled ?: 0,
        "neo_notification_channel" => $neoNotificationChannel ?: 'email',
        "neo_notification_days_in_advance" => $neoNotificationDaysInAdvance ?: 7
    ];
});
