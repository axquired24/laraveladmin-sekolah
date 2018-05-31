<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Sekolah::class, function (Faker $faker) {
	$sekolah = collect(['SMP', 'SMA']);
    return [
	    'name' =>  $sekolah->random() . ' ' . $faker->name,
		'address' => $faker->address,
		'bk_teacher' => $faker->name
    ];
});
