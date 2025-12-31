<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimensiWaktu extends Model
{
    protected $table = 'dimensi_waktu'; 
    protected $primaryKey = 'Date_of_Admission'; 
    public $incrementing = false;
    protected $keyType = 'string'; // atau 'date'
    public $timestamps = false;

    // HANYA IZINKAN KOLOM INI
    protected $fillable = [
        'Date_of_Admission'
    ];
}