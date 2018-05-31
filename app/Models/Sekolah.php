<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    function kelas() {
    	return $this->hasMany(Kelas::class);
    }
}
