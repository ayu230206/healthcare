<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $tahunDipilih = $request->query('tahun');

        // 1. TREN (Query murni ke Fact Table - Sangat Cepat)
        $tren = DB::table('fact_patients')
            ->select(
                DB::raw('YEAR(Date_of_Admission) as tahun'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tahun')
            ->orderBy('tahun', 'ASC')
            ->get();

        // LOGIKA FILTER DASAR
        // Kita gunakan fact_patients sebagai pusat data
        $queryBase = DB::table('fact_patients');
        if ($tahunDipilih) {
            $queryBase->whereYear('Date_of_Admission', $tahunDipilih);
        }

        // 2. HASIL TES (Menggunakan clone agar filter tahun konsisten)
        $hasilTes = (clone $queryBase)
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

        // 3. DEMOGRAFI (Relasi Bintang: Fact joined with Dimension)
        // Kita perbaiki join-nya agar lebih rapi
        $demografi = DB::table('fact_patients')
            ->join('dimensi_pasien', 'fact_patients.Name', '=', 'dimensi_pasien.Name') 
            ->select(
                'dimensi_pasien.Age_Group',
                DB::raw("SUM(CASE WHEN dimensi_pasien.Gender = 'Female' THEN 1 ELSE 0 END) as female"),
                DB::raw("SUM(CASE WHEN dimensi_pasien.Gender = 'Male' THEN 1 ELSE 0 END) as male")
            )
            ->when($tahunDipilih, function ($query, $tahunDipilih) {
                return $query->whereYear('fact_patients.Date_of_Admission', $tahunDipilih);
            })
            ->groupBy('dimensi_pasien.Age_Group')
            ->orderBy('dimensi_pasien.Age_Group', 'ASC')
            ->get();

        // 4. GOLONGAN DARAH
        $darah = DB::table('fact_patients')
            ->join('dimensi_pasien', 'fact_patients.Name', '=', 'dimensi_pasien.Name')
            ->select('dimensi_pasien.Blood_Type', DB::raw('COUNT(*) as total'))
            ->when($tahunDipilih, function ($query, $tahunDipilih) {
                return $query->whereYear('fact_patients.Date_of_Admission', $tahunDipilih);
            })
            ->groupBy('dimensi_pasien.Blood_Type')
            ->orderBy('total', 'DESC')
            ->get();

        // 5. TOTAL BILLING
        $totalBilling = (clone $queryBase)->sum('Billing_Amount');

        return view('dashboard', compact('tren', 'hasilTes', 'demografi', 'darah', 'totalBilling', 'tahunDipilih'));
    }
}