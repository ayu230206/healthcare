<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingAmountController extends Controller
{
    public function index(Request $request) // Tambahkan Request
    {
        $tahunDipilih = $request->query('tahun');

        // 1. Data Billing per Kondisi (DIFILTER TAHUN)
        $billingByCondition = DB::table('fact_patients')
            ->select('Medical_Condition', DB::raw('SUM(Billing_Amount) as total'))
            ->when($tahunDipilih, function ($query, $tahunDipilih) {
                return $query->whereYear('Date_of_Admission', $tahunDipilih);
            })
            ->groupBy('Medical_Condition')
            ->orderBy('total', 'DESC')
            ->get();

        // 2. Data Tren Tahunan (JANGAN DIFILTER agar grafiknya tetap utuh)
        $billingByYear = DB::table('fact_patients')
            ->select(
                DB::raw('YEAR(Date_of_Admission) as tahun'),
                DB::raw('SUM(Billing_Amount) as total')
            )
            ->groupBy('tahun')
            ->orderBy('tahun', 'ASC')
            ->get();

        return view('billing.index', compact('billingByCondition', 'billingByYear', 'tahunDipilih'));
    }
}