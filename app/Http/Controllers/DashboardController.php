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


    $queryBase = DB::table('fact_patients');
    if ($tahunDipilih) {
        $queryBase->whereYear('Date_of_Admission', $tahunDipilih);
    }

    $factData = $queryBase->select('Name', 'Date_of_Admission', 'Billing_Amount', 'Medical_Condition', 'Test_Results')->get();

    $dimensiRaw = DB::table('dimensi_pasien')->get();
    $mappedDimensi = [];
    foreach ($dimensiRaw as $d) {
        $nameKey = trim(strtolower($d->Name)); 
        if (!isset($mappedDimensi[$nameKey])) {
            $mappedDimensi[$nameKey] = $d;
        }
    }


    $countDarah = [];
    $countDemo = [];
    $totalBilling = 0;

    foreach ($factData as $f) {
        $totalBilling += $f->Billing_Amount;
        $nameKey = trim(strtolower($f->Name));
        $dim = $mappedDimensi[$nameKey] ?? null;


        $bt = $dim->Blood_Type ?? 'Unknown';
        $countDarah[$bt] = ($countDarah[$bt] ?? 0) + 1;


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


    $darah = collect($countDarah)->map(fn($val, $key) => (object)['Blood_Type' => $key, 'total' => $val])->values();
    $demografi = collect($countDemo)->map(fn($val, $key) => (object)['Age_Group' => $key, 'female' => $val['female'], 'male' => $val['male']])->sortBy('Age_Group')->values();
    

    $tren = DB::table('fact_patients')->select(DB::raw('YEAR(Date_of_Admission) as tahun'), DB::raw('COUNT(*) as total'))->groupBy('tahun')->orderBy('tahun', 'ASC')->get();
    
    $hasilTes = (clone $queryBase)->select('Medical_Condition', 
        DB::raw("SUM(CASE WHEN Test_Results LIKE 'Normal%' THEN 1 ELSE 0 END) as normal"),
        DB::raw("SUM(CASE WHEN Test_Results LIKE 'Inconclusive%' THEN 1 ELSE 0 END) as inconclusive"),
        DB::raw("SUM(CASE WHEN Test_Results LIKE 'Abnormal%' THEN 1 ELSE 0 END) as abnormal")
    )->groupBy('Medical_Condition')->orderByRaw('COUNT(*) DESC')->limit(6)->get();

    return view('dashboard', compact('tren', 'hasilTes', 'demografi', 'darah', 'totalBilling', 'tahunDipilih'));
}
}