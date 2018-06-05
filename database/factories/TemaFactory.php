<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Tema::class, function (Faker $faker) {
    return [
        'name' => $faker->lastName . ' ' . $faker->jobTitle,
	    'description' => $faker->paragraph
    ];
});
