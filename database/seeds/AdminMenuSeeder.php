<?php

use Illuminate\Database\Seeder;
use Encore\Admin\Auth\Database\Menu;

class AdminMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menu = new Menu();
        $menu->title = "Daftar Sekolah";
        $menu->icon = "fa-building";
        $menu->uri = "sekolah";
        $menu->save();

        echo "\nMenu $menu->title created successfully";
    }
}
