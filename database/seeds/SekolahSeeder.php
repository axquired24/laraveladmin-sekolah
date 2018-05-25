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
        // generate sekolah
	    $c_sekolah = 10;
	    $c_kelas = 3;

	    // Generate Sekolah, Kelas, Siswa
	    echo "Generate Sekolah, Kelas, Siswa\n";
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
}
