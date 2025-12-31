<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimensiPasien extends Model
{
    // Nama tabel master
    protected $table = 'dimensi_pasien'; 

    // Primary Key
    protected $primaryKey = 'Name';
    public $incrementing = false;
    protected $keyType = 'string';

    // Matikan timestamp
    public $timestamps = false;

    // DAFTAR KOLOM YANG BOLEH DIISI (Mass Assignable)
    protected $fillable = [
        'Name', 
        'Gender', 
        'Age', 
        'Blood_Type',
        'Age_Group' // <--- WAJIB DITAMBAHKAN DI SINI!
    ];
}