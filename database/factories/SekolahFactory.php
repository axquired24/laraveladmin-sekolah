<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Sekolah::class, function (Faker $faker) {
    return [
	    'name' => $faker->name,
		'address' => $faker->address,
		'bk_teacher' => $faker->name
    ];
});
