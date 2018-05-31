<?php

namespace App\Http\Collection;

class RouteCollection {
	public static $sekolah  = 'admin/sekolah';
	public static $kelas  = 'admin/kelas';
	public static $siswa  = 'admin/siswa';

	const CREATE = "create";
	const STORE = "store";
	const SHOW = "show";
	const EDIT = "edit";
	const UPDATE = "update";
	const DESTROY = "destroy";
	const INDEX = "index";
}