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
use Faker\Generator as Faker;

$factory->define(App\Product::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->numberBetween($min = 1000, $max = 9999),
        'name' => $faker->text(50),
        'free_shipping' => $faker->numberBetween($min = 0, $max = 1),
        'description' => $faker->text(255),
        'price' => $faker->randomFloat(2, $min = 0.00, $max = 9999.99),
        'category_id' => $faker->randomDigit,
    ];
});
