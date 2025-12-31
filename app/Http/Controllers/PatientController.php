<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\DimensiPasien;
use App\Models\DimensiWaktu; // <--- WAJIB ADA: Model untuk tanggal
use Illuminate\Http\Request;

class PatientController extends Controller
{
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
        $conditions = Patient::select('Medical_Condition')
                             ->distinct()
                             ->pluck('Medical_Condition');

        // 3. Pagination
        $patients = $query->paginate(10)->withQueryString();

        return view('patients.index', compact('patients', 'conditions'));
    }

    public function create()
    {
        return view('patients.form');
    }

    public function store(Request $request)
    {
        // 1. Validasi Lengkap
        $validated = $request->validate([
            'Name'              => 'required|string|max:100',
            'Gender'            => 'required|string',
            'Age'               => 'required|integer',
            'Blood_Type'        => 'nullable|string',
            'Age_Group'         => 'required|string', 
            'Medical_Condition' => 'required|string|max:100',
            'Date_of_Admission' => 'required|date',
            'Length_of_Stay'    => 'required|integer',
            'Billing_Amount'    => 'required|numeric',
            'Test_Results'      => 'required|string|max:255',
        ]);

        // 2. SIMPAN KE DIMENSI PASIEN (Master Data Pasien)
        DimensiPasien::updateOrCreate(
            ['Name' => $request->Name], // Kunci pencarian (Nama)
            [
                'Gender'     => $request->Gender,
                'Age'        => $request->Age,
                'Blood_Type' => $request->Blood_Type,
                'Age_Group'  => $request->Age_Group 
            ]
        );

        // 3. SIMPAN KE DIMENSI WAKTU (PENYELAMAT ERROR FOREIGN KEY)
        // Cek apakah tanggal ini sudah ada? Jika belum, buatkan.
        DimensiWaktu::firstOrCreate([
            'Date_of_Admission' => $request->Date_of_Admission
        ]);

        // 4. SIMPAN KE FACT TABLE (Data Rawat Inap)
        // Buang data dimensi (Gender, Age, dll) sebelum masuk tabel fact
        $factData = collect($validated)
            ->except(['Gender', 'Age', 'Blood_Type', 'Age_Group']) 
            ->toArray();
        
        Patient::create($factData);

        return redirect()->route('patients.index')->with('success', 'Data lengkap berhasil ditambahkan.');
    }

    public function edit($name)
    {
        $patient = Patient::where('Name', $name)->firstOrFail();
        return view('patients.form', compact('patient'));
    }

    public function update(Request $request, $name)
    {
        $patient = Patient::where('Name', $name)->firstOrFail();
        
        $validated = $request->validate([
            'Name'              => 'required|string|max:100',
            'Gender'            => 'required|string',
            'Age'               => 'required|integer',
            'Blood_Type'        => 'nullable|string',
            'Age_Group'         => 'required|string',
            'Medical_Condition' => 'required|string|max:100',
            'Date_of_Admission' => 'required|date',
            'Length_of_Stay'    => 'required|integer',
            'Billing_Amount'    => 'required|numeric',
            'Test_Results'      => 'required|string|max:255',
        ]);

        // 1. Update Dimensi Pasien
        DimensiPasien::updateOrCreate(
            ['Name' => $request->Name],
            [
                'Gender'     => $request->Gender,
                'Age'        => $request->Age,
                'Blood_Type' => $request->Blood_Type,
                'Age_Group'  => $request->Age_Group 
            ]
        );

        // 2. Update Dimensi Waktu
        // Pastikan tanggal baru terdaftar jika user mengubah tanggal
        DimensiWaktu::firstOrCreate([
            'Date_of_Admission' => $request->Date_of_Admission
        ]);

        // 3. Update Fact Table
        $factData = collect($validated)
            ->except(['Gender', 'Age', 'Blood_Type', 'Age_Group']) 
            ->toArray();
        
        $patient->update($factData);
        
        return redirect()->route('patients.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($name)
    {
        $patient = Patient::where('Name', $name)->firstOrFail();
        $patient->delete();
        
        return redirect()->route('patients.index')->with('success', 'Data dihapus.');
    }
}