<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Siswa::class, function (Faker $faker) {
	$genders = collect('m', 'f');
    return [
	    'kelas_id' => 1,
		'name' => $faker->name,
		'nik' => $faker->bankAccountNumber,
		'gender' => $genders->random()
    ];
});
