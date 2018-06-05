<?php

use App\Models\Hasil;
use Illuminate\Database\Seeder;
use App\Models\Sekolah;
use App\Models\Kelas;
use App\Models\Siswa;

class SekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	echo "Truncate Sekolah, Kelas, Siswa ...\n";
    	$this->truncateTable();

	    // generate sekolah
	    $c_sekolah = 10;

	    // Generate Sekolah, Kelas, Siswa
	    echo "\nGenerate Sekolah, Kelas, Siswa\n";
	    factory(Sekolah::class, $c_sekolah)->create()
		    ->each(function($sekolah) {
			    echo "Creating Sekolah: $sekolah->name ...\n";
		    	factory(Kelas::class, rand(2, 5))->create([
		    		'sekolah_id' => $sekolah->id
			    ])

			    ->each(function($kelas) {
				    echo "Creating Kelas: $kelas->name\n";
			    	factory(Siswa::class, rand(10, 20))->create([
					    'kelas_id' => $kelas->id
				    ])

				    ->each(function($siswa) {
				    	$this->genDummyHasil($siswa);
					    echo "Creating Siswa & Hasil: $siswa->name\n";
				    });
			    });
		    });
    }

    function genDummyHasil(Siswa $siswa) {
    	$tema_ids = \App\Models\Tema::pluck('id')->random(10)->toJson();

    	$hasil = new Hasil();
    	$hasil->siswa_id = $siswa->id;
    	$hasil->answer = $tema_ids;
    	$hasil->save();
    }

    function truncateTable() {
	    //disable foreign key check for this connection before running seeders
	    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

	    Sekolah::truncate();
	    Kelas::truncate();
	    Siswa::truncate();
	    // reset foreign check
	    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
