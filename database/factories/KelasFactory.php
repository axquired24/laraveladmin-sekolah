<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Kelas::class, function (Faker $faker) {
	$school_year = $faker->numberBetween(2010, 2017);
	$school_year = $school_year . '/' . ($school_year+1);
	$kelas_int = $faker->numberBetween(7, 12);
    return [
	    'sekolah_id' => 1, // will be override
		'name' => $kelas_int .' ' . $faker->city,
		'school_year' => $school_year
    ];
});
