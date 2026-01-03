<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Menampilkan daftar data pasien (Fact Table).
     * Sesuai prinsip Data Warehouse: Read-Only.
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // 1. Logic Search (Berdasarkan Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('Name', 'like', "%{$search}%");
        }

        // 2. Logic Filter (Berdasarkan Medical Condition)
        if ($request->filled('condition')) {
            $query->where('Medical_Condition', $request->condition);
        }

        // Ambil daftar penyakit unik untuk dropdown filter
        // Ini membantu user melakukan analisis data historis
        $conditions = Patient::select('Medical_Condition')
            ->distinct()
            ->pluck('Medical_Condition');

        // 3. Pagination (Menampilkan 10 data per halaman agar rapi)
        $patients = $query->paginate(10)->withQueryString();

        return view('patients.index', compact('patients', 'conditions'));
    }

    /* Fungsi create, store, edit, update, dan destroy 
       telah dihapus untuk menjaga integritas Data Warehouse 
       agar bersifat Non-Volatile.
    */
}