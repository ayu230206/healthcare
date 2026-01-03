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
            ->select(
                'Medical_Condition',
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

        // 4. DEMOGRAFI (GENDER & AGE GROUP) - Langsung dari tabel dimensi
        $demografi = DB::table('dimensi_pasien')
            ->select(
                'Age_Group',
                DB::raw("SUM(CASE WHEN Gender = 'Female' THEN 1 ELSE 0 END) as female"),
                DB::raw("SUM(CASE WHEN Gender = 'Male' THEN 1 ELSE 0 END) as male")
            )
            ->groupBy('Age_Group')
            ->orderBy('Age_Group', 'ASC')
            ->get();

        // 5. GOLONGAN DARAH - Langsung dari tabel dimensi
        $darah = DB::table('dimensi_pasien')
            ->select('Blood_Type', DB::raw('COUNT(*) as total'))
            ->groupBy('Blood_Type')
            ->orderBy('total', 'DESC')
            ->get();

        // 5. GOLONGAN DARAH (Langsung dari dimensi agar tidak double)
        $darah = DB::table('dimensi_pasien')
            ->select('Blood_Type', DB::raw('COUNT(*) as total'))
            ->groupBy('Blood_Type')
            ->orderBy('total', 'DESC') // Urutkan dari yang terbanyak
            ->get();

        //6
        $totalBilling = DB::raw('SELECT SUM(Billing_Amount) as total FROM fact_patients');
        $totalBilling = DB::table('fact_patients')->sum('Billing_Amount');
        // PERBAIKAN DISINI: Tambahkan 'darah' ke dalam compact
        return view('dashboard', compact('tren', 'hasilTes', 'kondisi', 'demografi', 'darah', 'totalBilling'));
    }
}