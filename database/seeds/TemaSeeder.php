<?php

use App\Models\Tema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "Generate Random Tema";
        $this->truncateTable();
        factory(Tema::class, 15)->create();
    }

	function truncateTable() {
		//disable foreign key check for this connection before running seeders
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		Tema::truncate();
		// reset foreign check
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}
}
