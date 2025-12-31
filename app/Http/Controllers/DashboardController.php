<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        
        // 1. TREN 
        $tren = DB::table('fact_patients')
            ->select(
                DB::raw('YEAR(Date_of_Admission) as tahun'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tahun')
            ->orderBy('tahun', 'ASC')
            ->get();

        // 2. HASIL TES (SOLUSI FINAL - ANTI \r DAN SPASI)
        // Menggunakan LIKE 'Kata%' agar karakter sampah (\r, \n, spasi) di belakang diabaikan
        $hasilTes = DB::table('fact_patients')
            ->select('Medical_Condition',
                DB::raw("SUM(CASE WHEN Test_Results LIKE 'Normal%' THEN 1 ELSE 0 END) as normal"),
                DB::raw("SUM(CASE WHEN Test_Results LIKE 'Inconclusive%' THEN 1 ELSE 0 END) as inconclusive"),
                DB::raw("SUM(CASE WHEN Test_Results LIKE 'Abnormal%' THEN 1 ELSE 0 END) as abnormal")
            )
            ->groupBy('Medical_Condition')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(6)
            ->get();

        // 3. KONDISI MEDIS
        $kondisi = DB::table('fact_patients')
            ->select('Medical_Condition', DB::raw('COUNT(*) as total'))
            ->groupBy('Medical_Condition')
            ->orderBy('total', 'desc')
            ->get();

        // 4. DEMOGRAFI (GENDER & AGE GROUP)
        $demografi = DB::table('fact_patients')
            ->join('dimensi_pasien', 'fact_patients.Name', '=', 'dimensi_pasien.Name') 
            ->select(
                'dimensi_pasien.Age_Group', 
                DB::raw("SUM(CASE WHEN dimensi_pasien.Gender = 'Female' THEN 1 ELSE 0 END) as female"),
                DB::raw("SUM(CASE WHEN dimensi_pasien.Gender = 'Male' THEN 1 ELSE 0 END) as male")
            )
            ->groupBy('dimensi_pasien.Age_Group')
            ->orderBy('dimensi_pasien.Age_Group', 'ASC')
            ->get();
            
        // 5. GOLONGAN DARAH (Query kamu sudah benar disini)
        $darah = DB::table('fact_patients')
            ->join('dimensi_pasien', 'fact_patients.Name', '=', 'dimensi_pasien.Name')
            ->select('dimensi_pasien.Blood_Type', DB::raw('COUNT(*) as total'))
            ->groupBy('dimensi_pasien.Blood_Type')
            ->get();
        
        //6
        $totalBilling = DB::raw('SELECT SUM(Billing_Amount) as total FROM fact_patients');
$totalBilling = DB::table('fact_patients')->sum('Billing_Amount');
        // PERBAIKAN DISINI: Tambahkan 'darah' ke dalam compact
        return view('dashboard', compact('tren', 'hasilTes', 'kondisi', 'demografi', 'darah','totalBilling'));
    }
}