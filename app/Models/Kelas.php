<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    function sekolah() {
    	return $this->belongsTo(Sekolah::class);
    }

    function siswa() {
    	return $this->hasMany(Siswa::class);
    }
}
