<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'role' => 'coach',
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Team::class, function (Faker\Generator $faker) {

    $name = $faker->company;

    return [
        'name' => $name,
        'slug' => str_slug($name, '-'),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Round::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'default_date' => $faker->date(),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Player::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
    ];
});
