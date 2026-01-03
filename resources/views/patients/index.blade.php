@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="sm:flex sm:items-center justify-between border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Data Pasien (Fact Table)</h1>
            <p class="mt-1 text-sm text-slate-500 italic text-blue-600">
                Mode: Non-Volatile 
            </p>
        </div>
    </div>

    {{-- Section Filter Tetap Dipertahankan untuk Analisis --}}
    <div class="mt-6 bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
        <form action="{{ route('patients.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            
            <div class="relative flex-grow">
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="block w-full rounded-md border-slate-300 pl-4 pr-10 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 border shadow-sm" 
                       placeholder="Cari Nama Pasien...">
            </div>

            <div class="min-w-[200px]">
                <select name="condition" onchange="this.form.submit()" 
                        class="block w-full rounded-md border-slate-300 py-2 pl-3 pr-10 text-base focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm border shadow-sm">
                    <option value="">Semua Kondisi</option>
                    @foreach($conditions as $cond)
                        <option value="{{ $cond }}" {{ request('condition') == $cond ? 'selected' : '' }}>
                            {{ $cond }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 border border-slate-300">
                Filter
            </button>
            
            @if(request()->filled('search') || request()->filled('condition'))
                <a href="{{ route('patients.index') }}" class="rounded-md bg-red-50 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-100 border border-red-200 flex items-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="mt-6 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-slate-300 bg-white">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-bold text-slate-900 sm:pl-6">Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-slate-900">Medical Condition</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-slate-900">Admission Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-slate-900">Stay (Days)</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-slate-900">Billing Amount</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-slate-900">Test Results</th>
                                {{-- Kolom Aksi Sudah Dihapus --}}
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($patients as $patient)
                            <tr class="hover:bg-slate-50 transition-colors text-slate-600">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 sm:pl-6">
                                    {{ $patient->Name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                        {{ $patient->Medical_Condition }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-mono">
                                    {{ $patient->Date_of_Admission }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    {{ $patient->Length_of_Stay }} Hari
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-slate-900">
                                    {{ number_format($patient->Billing_Amount, 0, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm uppercase">
                                    {{ $patient->Test_Results }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-3 py-12 text-center text-sm text-slate-500 bg-slate-50">
                                    <p class="font-medium text-slate-900 italic">Data tidak tersedia di Data Warehouse.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $patients->links() }}
    </div>

</div>
@endsection