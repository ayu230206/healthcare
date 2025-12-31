<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'fact_patients';

    protected $primaryKey = 'Name';
    public $incrementing = false;   // Karena 'Name' bukan angka yang auto-increment
    protected $keyType = 'string';  // Karena 'Name' adalah string (VARCHAR)
    public $timestamps = false;
    protected $fillable = [
        'Name',
        'Medical_Condition',
        'Date_of_Admission',
        'Length_of_Stay',
        'Billing_Amount',
        'Test_Results',
    ];
    public function dimensi()
{
    // Menghubungkan Fact Table ke Dimensi Table lewat kolom 'Name'
    return $this->belongsTo(DimensiPasien::class, 'Name', 'Name');
}
}