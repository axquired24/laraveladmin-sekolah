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
    	$menus = collect([
		    [
			    'title' => 'Daftar Sekolah',
			    'icon' => 'fa-building',
			    'uri' => 'sekolah'
		    ],
		    [
			    'title' => 'Daftar Tema',
			    'icon' => 'fa-cubes',
			    'uri' => 'tema'
		    ]
	    ]);

        $menus->each(function($item) {
        	$item = (object) $item;

	        $menu = new Menu();
	        $menu->title = $item->title;
	        $menu->icon = $item->icon;
	        $menu->uri = $item->uri;
	        $menu->save();

	        echo "\nMenu $menu->title created successfully";
        });
        echo "\n";
    }
}
