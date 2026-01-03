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

    // --- 1. AMBIL SEMUA DATA FAKTA (Tanpa Distinct agar tidak hilang 5000 data) ---
    $queryBase = DB::table('fact_patients');
    if ($tahunDipilih) {
        $queryBase->whereYear('Date_of_Admission', $tahunDipilih);
    }
    
    // Ambil semua kolom yang dibutuhkan tanpa distinct()
    $factData = $queryBase->select('Name', 'Date_of_Admission', 'Billing_Amount', 'Medical_Condition', 'Test_Results')->get();

    // --- 2. AMBIL DATA DIMENSI & MAPPING (Kunci agar tidak ngali 2) ---
    $dimensiRaw = DB::table('dimensi_pasien')->get();
    $mappedDimensi = [];
    foreach ($dimensiRaw as $d) {
        $nameKey = trim(strtolower($d->Name)); // Gunakan lowercase agar lebih aman
        if (!isset($mappedDimensi[$nameKey])) {
            $mappedDimensi[$nameKey] = $d;
        }
    }

    // --- 3. PROSES DATA ---
    $countDarah = [];
    $countDemo = [];
    $totalBilling = 0;

    foreach ($factData as $f) {
        $totalBilling += $f->Billing_Amount;
        $nameKey = trim(strtolower($f->Name));
        $dim = $mappedDimensi[$nameKey] ?? null;

        // Hitung Golongan Darah
        $bt = $dim->Blood_Type ?? 'Unknown';
        $countDarah[$bt] = ($countDarah[$bt] ?? 0) + 1;

        // Hitung Demografi
        $ag = $dim->Age_Group ?? 'Unknown';
        $gender = $dim->Gender ?? 'Unknown';
        
        if (!isset($countDemo[$ag])) {
            $countDemo[$ag] = ['female' => 0, 'male' => 0];
        }
        
        if ($gender == 'Female') {
            $countDemo[$ag]['female']++;
        } elseif ($gender == 'Male') {
            $countDemo[$ag]['male']++;
        }
    }

    // Format untuk Chart
    $darah = collect($countDarah)->map(fn($val, $key) => (object)['Blood_Type' => $key, 'total' => $val])->values();
    $demografi = collect($countDemo)->map(fn($val, $key) => (object)['Age_Group' => $key, 'female' => $val['female'], 'male' => $val['male']])->sortBy('Age_Group')->values();
    
    // Data Tren & Hasil Tes (Murni dari fact table)
    $tren = DB::table('fact_patients')->select(DB::raw('YEAR(Date_of_Admission) as tahun'), DB::raw('COUNT(*) as total'))->groupBy('tahun')->orderBy('tahun', 'ASC')->get();
    
    $hasilTes = (clone $queryBase)->select('Medical_Condition', 
        DB::raw("SUM(CASE WHEN Test_Results LIKE 'Normal%' THEN 1 ELSE 0 END) as normal"),
        DB::raw("SUM(CASE WHEN Test_Results LIKE 'Inconclusive%' THEN 1 ELSE 0 END) as inconclusive"),
        DB::raw("SUM(CASE WHEN Test_Results LIKE 'Abnormal%' THEN 1 ELSE 0 END) as abnormal")
    )->groupBy('Medical_Condition')->orderByRaw('COUNT(*) DESC')->limit(6)->get();

    return view('dashboard', compact('tren', 'hasilTes', 'demografi', 'darah', 'totalBilling', 'tahunDipilih'));
}
}