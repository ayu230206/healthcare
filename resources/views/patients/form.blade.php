@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">
            {{ isset($patient) ? 'Edit Data Pasien' : 'Tambah Pasien Baru' }}
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Silakan isi formulir di bawah ini dengan lengkap.
        </p>
    </div>

    <div class="bg-white shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl md:col-span-2">
        
        <form action="{{ isset($patient) ? route('patients.update', $patient->Name) : route('patients.store') }}" method="POST">
            @csrf
            @if(isset($patient))
                @method('PUT')
            @endif

            <div class="px-4 py-6 sm:p-8">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <div class="sm:col-span-6 md:col-span-4">
                        <label for="Name" class="block text-sm font-medium leading-6 text-slate-900">Nama Pasien</label>
                        <div class="mt-2">
                            <input type="text" name="Name" id="Name" 
                                   value="{{ old('Name', $patient->Name ?? '') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 pl-3">
                            @error('Name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @if(isset($patient))
                            <p class="mt-1 text-xs text-slate-500">Info: Mengubah nama akan otomatis membuat data pasien baru jika nama belum terdaftar.</p>
                        @endif
                    </div>

                    <div class="sm:col-span-2">
                        <label for="Gender" class="block text-sm font-medium leading-6 text-slate-900">Gender</label>
                        <div class="mt-2">
                            <select name="Gender" id="Gender" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 pl-3">
                                <option value="" disabled selected>Pilih Gender</option>
                                @php $g = old('Gender', $patient->dimensi->Gender ?? ''); @endphp
                                <option value="Male" {{ $g == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $g == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('Gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="Age" class="block text-sm font-medium leading-6 text-slate-900">Umur</label>
                        <div class="mt-2">
                            <input type="number" name="Age" id="Age" 
                                   value="{{ old('Age', $patient->dimensi->Age ?? '') }}"
                                   placeholder="Contoh: 45"
                                   class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 pl-3">
                            @error('Age')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="Age_Group" class="block text-sm font-medium leading-6 text-slate-900">Kategori Umur</label>
                        <div class="mt-2">
                            <select name="Age_Group" id="Age_Group" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 bg-gray-50 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 pl-3">
                                <option value="" disabled selected>-- Otomatis --</option>
                                @php $ag = old('Age_Group', $patient->dimensi->Age_Group ?? ''); @endphp
                                <option value="Child" {{ $ag == 'Child' ? 'selected' : '' }}>Child (0-14)</option>
                                <option value="Adult" {{ $ag == 'Adult' ? 'selected' : '' }}>Adult (15-64)</option>
                                <option value="Senior" {{ $ag == 'Senior' ? 'selected' : '' }}>Senior (65+)</option>
                            </select>
                            @error('Age_Group')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="Blood_Type" class="block text-sm font-medium leading-6 text-slate-900">Gol. Darah</label>
                        <div class="mt-2">
                            <select name="Blood_Type" id="Blood_Type" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 pl-3">
                                <option value="" disabled selected>Pilih</option>
                                @php $bt = old('Blood_Type', $patient->dimensi->Blood_Type ?? ''); @endphp
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                                    <option value="{{ $type }}" {{ $bt == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('Blood_Type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="Medical_Condition" class="block text-sm font-medium leading-6 text-slate-900">Medical Condition</label>
                        <div class="mt-2">
                            <input type="text" name="Medical_Condition" id="Medical_Condition" 
                                   value="{{ old('Medical_Condition', $patient->Medical_Condition ?? '') }}"
                                   placeholder="Contoh: Cancer, Diabetes"
                                   class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 pl-3">
                            @error('Medical_Condition')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="Date_of_Admission" class="block text-sm font-medium leading-6 text-slate-900">Tanggal Masuk</label>
                        <div class="mt-2">
                            <input type="date" name="Date_of_Admission" id="Date_of_Admission" 
                                   value="{{ old('Date_of_Admission', $patient->Date_of_Admission ?? '') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 pl-3">
                            @error('Date_of_Admission')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="Length_of_Stay" class="block text-sm font-medium leading-6 text-slate-900">Lama Inap (Hari)</label>
                        <div class="mt-2">
                            <input type="number" name="Length_of_Stay" id="Length_of_Stay" 
                                   value="{{ old('Length_of_Stay', $patient->Length_of_Stay ?? '') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 pl-3">
                            @error('Length_of_Stay')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="Billing_Amount" class="block text-sm font-medium leading-6 text-slate-900">Tagihan ($)</label>
                        <div class="mt-2">
                            <input type="number" step="0.01" name="Billing_Amount" id="Billing_Amount" 
                                   value="{{ old('Billing_Amount', $patient->Billing_Amount ?? '') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 pl-3">
                            @error('Billing_Amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="Test_Results" class="block text-sm font-medium leading-6 text-slate-900">Hasil Tes</label>
                        <div class="mt-2">
                            <select name="Test_Results" id="Test_Results" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 pl-3">
                                <option value="" disabled selected>Pilih Hasil</option>
                                @php
                                    $currentResult = old('Test_Results', $patient->Test_Results ?? '');
                                @endphp
                                <option value="Normal" {{ $currentResult == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Abnormal" {{ $currentResult == 'Abnormal' ? 'selected' : '' }}>Abnormal</option>
                                <option value="Inconclusive" {{ $currentResult == 'Inconclusive' ? 'selected' : '' }}>Inconclusive</option>
                            </select>
                            @error('Test_Results')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <div class="flex items-center justify-end gap-x-6 border-t border-slate-900/10 px-4 py-4 sm:px-8">
                <a href="{{ route('patients.index') }}" class="text-sm font-semibold leading-6 text-slate-900">Batal</a>
                <button type="submit" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    {{ isset($patient) ? 'Simpan Perubahan' : 'Simpan Data Baru' }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT OTOMATIS PENGISIAN AGE GROUP --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ageInput = document.getElementById('Age');
        const groupSelect = document.getElementById('Age_Group');

        if(ageInput && groupSelect){
            // Fungsi untuk menentukan group
            function setAgeGroup() {
                let age = parseInt(ageInput.value);
                
                if (!isNaN(age)) {
                    if (age <= 14) {
                        groupSelect.value = "Child";
                    } else if (age >= 65) {
                        groupSelect.value = "Senior";
                    } else {
                        groupSelect.value = "Adult";
                    }
                }
            }

            // Jalankan saat user mengetik
            ageInput.addEventListener('input', setAgeGroup);
            
            // Jalankan sekali saat halaman dimuat (untuk mode Edit)
            // Cek jika Age Group masih kosong, baru isi otomatis
            if(groupSelect.value === "") {
                setAgeGroup();
            }
        }
    });
</script>

@endsection