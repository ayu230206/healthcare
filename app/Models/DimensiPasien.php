<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DimensiPasien extends Model
{

    protected $table = 'dimensi_pasien'; 

    protected $primaryKey = 'Name';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'Name', 
        'Gender', 
        'Age', 
        'Blood_Type',
        'Age_Group' 
    ];
}