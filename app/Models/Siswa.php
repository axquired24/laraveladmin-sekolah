<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    function kelas() {
    	return $this->belongsTo(Kelas::class);
    }

    function hasil() {
    	return $this->hasOne(Hasil::class);
    }
}
