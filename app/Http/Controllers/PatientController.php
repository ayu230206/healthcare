<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{

    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('Name', 'like', "%{$search}%");
        }


        if ($request->filled('condition')) {
            $query->where('Medical_Condition', $request->condition);
        }

        $conditions = Patient::select('Medical_Condition')
            ->distinct()
            ->pluck('Medical_Condition');

        $patients = $query->paginate(10)->withQueryString();

        return view('patients.index', compact('patients', 'conditions'));
    }


}