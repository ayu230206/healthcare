<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingAmountController extends Controller
{
    public function index()
{
    $billingByCondition = DB::table('fact_patients')
        // Kita gunakan LOG10 supaya angka raksasa dan angka kecil bisa tampil dalam satu grafik
        ->select('Medical_Condition', DB::raw('LOG10(SUM(Billing_Amount)) as total'))
        ->groupBy('Medical_Condition')
        ->get();

    // 2. Data Line Chart
    $billingByYear = DB::table('fact_patients')
        ->select(
            DB::raw('YEAR(Date_of_Admission) as tahun'),
            DB::raw('ROUND(SUM(Billing_Amount), 0) as total')
        )
        ->groupBy('tahun')
        ->orderBy('tahun', 'ASC')
        ->get();

    return view('billing.index', compact('billingByCondition', 'billingByYear'));
}
}