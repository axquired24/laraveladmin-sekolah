<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(SekolahSeeder::class);
         $this->call(AdminMenuSeeder::class);
         $this->call(TemaSeeder::class);
    }
}
