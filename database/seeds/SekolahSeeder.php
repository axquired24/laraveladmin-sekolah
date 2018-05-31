<?php

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
	    $c_kelas = 3;

	    // Generate Sekolah, Kelas, Siswa
	    echo "\nGenerate Sekolah, Kelas, Siswa\n";
	    factory(Sekolah::class, $c_sekolah)->create()
		    ->each(function($sekolah) use($c_kelas) {
			    echo "Creating Sekolah: $sekolah->name ...\n";
		    	factory(Kelas::class, $c_kelas)->create([
		    		'sekolah_id' => $sekolah->id
			    ])

			    ->each(function($kelas) {
				    echo "Creating Kelas: $kelas->name\n";
			    	factory(Siswa::class, $kelas->student_count)->create([
					    'kelas_id' => $kelas->id
				    ])

				    ->each(function($siswa) {
					    echo "Creating Siswa: $siswa->name\n";
				    });
			    });
		    });
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
