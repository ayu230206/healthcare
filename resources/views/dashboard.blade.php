@extends('layouts.app')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            width: 100%;
            cursor: pointer;
            transition: transform 0.2s, border-color 0.2s;
        }
        
        .card:hover {
            border-color: #6366f1;
            transform: translateY(-2px);
        }

        .analysis-content {
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease-in-out;
            opacity: 0;
        }

        .analysis-content.active {
            max-height: 1000px;
            opacity: 1;
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 2px dashed #f1f5f9;
        }

        canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>

    <div class="p-4 md:p-8 text-slate-700 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-6 md:space-y-8">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex flex-col">
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">Dashboard Analisis Kesehatan</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-sm md:text-base text-slate-500">
                            Laporan visual data pasien periode 2019-2024.
                        </p>
                        @if(request('tahun'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                             bg-amber-100 text-amber-800 border border-amber-200">
                                <i class="fas fa-filter mr-1"></i> Terfilter: {{ request('tahun') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if(request('tahun'))
                        <a href="{{ route('dashboard.index') }}" 
                           class="flex items-center gap-2 px-4 py-2 bg-rose-500 hover:bg-rose-600 
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-all">
                            Reset Filter
                        </a>
                    @endif
                    <p class="text-xs text-slate-400 hidden md:block italic">Klik card untuk melihat analisis mendalam</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500 flex flex-col justify-center">
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-wider">Total Patients</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($tren->sum('total')) }}</p>
                </div>

                <a href="{{ route('billing.index') }}" class="block no-underline">
                    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500 flex flex-col justify-center h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold text-green-500 uppercase tracking-wider">Total Billing Amount</p>
                                <p class="text-2xl font-bold text-slate-800">
                                    ${{ number_format($totalBilling / 1000000000000000, 2) }} Q
                                </p>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="w-full">
                <div class="card" onclick="toggleAnalysis('analysis-tren')">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-slate-700">Tren Pasien Tahunan</h2>
                        <i class="fas fa-plus-circle text-blue-500" id="icon-analysis-tren"></i>
                    </div>
                    <div class="relative w-full h-[300px] md:h-[400px]">
                        <canvas id="chartTren"></canvas>
                    </div>
                    <div id="analysis-tren" class="analysis-content">
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <h4 class="font-bold text-blue-800 mb-2">💡 Analisis Tren Volume Pasien:</h4>
                            <ul class="list-disc ml-5 text-sm text-blue-900 space-y-1">
                                <li><b>Puncak Pertumbuhan:</b> Terjadi lonjakan signifikan dari 2019 ke 2020.</li>
                                <li><b>Stabilitas:</b> Periode 2020-2023 menunjukkan volume yang sangat stabil (rata-rata 11rb pasien/tahun), mengindikasikan kapasitas layanan telah mencapai optimal.</li>
                                <li><b>Laporan (2024):</b> Penurunan volume pada grafik disebabkan oleh periode laporan yang baru mencakup data hingga Mei 2024. Secara proyeksi, angka kunjungan diprediksi tetap stabil dan konsisten dengan tahun sebelumnya jika tren bulanan berjalan normal.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                <div class="card" onclick="toggleAnalysis('analysis-demo')">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-slate-700">Demografi Gender & Umur</h2>
                        <i class="fas fa-plus-circle text-pink-500" id="icon-analysis-demo"></i>
                    </div>
                    <div class="relative w-full h-[300px] md:h-[350px]">
                        <canvas id="chartDemografi"></canvas>
                    </div>
                    <div id="analysis-demo" class="analysis-content">
                        <div class="bg-pink-50 p-4 rounded-lg border-l-4 border-pink-500">
                            <h4 class="font-bold text-pink-800 mb-2">💡 Analisis Demografi:</h4>
                            <p class="text-sm text-pink-900 leading-relaxed">
                                Distribusi gender antara pasien laki-laki dan perempuan menunjukkan keseimbangan yang signifikan di seluruh kategori usia. Hal ini merefleksikan inklusivitas layanan kesehatan dalam menjangkau seluruh lapisan masyarakat tanpa diskriminasi gender. Strategi operasional dapat difokuskan pada optimalisasi layanan bagi kelompok usia dewasa yang merupakan basis populasi pasien terbesar.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card" onclick="toggleAnalysis('analysis-darah')">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-slate-700">Proporsi Golongan Darah</h2>
                        <i class="fas fa-plus-circle text-purple-500" id="icon-analysis-darah"></i>
                    </div>
                    <div class="relative w-full h-[300px] md:h-[350px] flex justify-center items-center">
                        <canvas id="chartDarah"></canvas>
                    </div>
                    <div id="analysis-darah" class="analysis-content">
                        <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                            <h4 class="font-bold text-purple-800 mb-2">💡 Analisis Logistik Darah:</h4>
                            <p class="text-sm text-purple-900 leading-relaxed">
                                Rasio golongan darah cenderung merata. Insight ini sangat berguna bagi bagian <b>Blood Bank</b> untuk menjaga stok plasma darah tanpa terjadi penumpukan pada satu golongan darah tertentu, mengurangi risiko pembuangan stok yang kedaluwarsa.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full">
                <div class="card" onclick="toggleAnalysis('analysis-kondisi')">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-slate-700">Hasil Tes Berdasarkan Kondisi Medis</h2>
                        <i class="fas fa-plus-circle text-teal-500" id="icon-analysis-kondisi"></i>
                    </div>
                    <div class="relative w-full h-[500px] md:h-[650px]">
                        <canvas id="chartKondisi"></canvas>
                    </div>
                    <div id="analysis-kondisi" class="analysis-content">
                        <div class="bg-teal-50 p-4 rounded-lg border-l-4 border-teal-500">
                            <h4 class="font-bold text-teal-800 mb-2">💡 Analisis Klinis Kondisi Medis:</h4>
                            <p class="text-sm text-teal-900 mb-2">Data menunjukkan variasi hasil tes (Normal, Inconclusive, Abnormal) yang cukup dinamis di setiap penyakit.</p>
                            <ul class="list-disc ml-5 text-sm text-teal-900">
                                <li><b>Prioritas:</b> Kondisi medis dengan bar <i>Abnormal (Merah)</i> tertinggi memerlukan protokol penanganan pasca-tes yang lebih ketat.</li>
                                <li><b>Efisiensi Diagnostik:</b> Tingginya hasil <i>Inconclusive</i> pada beberapa kategori menyarankan perlunya peningkatan akurasi alat tes atau prosedur pengambilan sampel.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>

        function toggleAnalysis(id) {
            const content = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            
            content.classList.toggle('active');
            
            if (content.classList.contains('active')) {
                icon.classList.replace('fa-plus-circle', 'fa-minus-circle');
            } else {
                icon.classList.replace('fa-minus-circle', 'fa-plus-circle');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            Chart.register(ChartDataLabels);

            const trenData = @json($tren);
            const demografiData = @json($demografi);
            const darahData = @json($darah);
            const tahunAktif = @json($tahunDipilih);

            const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(num);

            const palette = {
                trend: '#6366f1',
                active: '#f59e0b',
                female: '#f9a8d4', male: '#7dd3fc',
                blood: ['#3b5998', '#607d8b', '#8e44ad', '#00acc1', '#795548', '#546e7a', '#9575cd', '#455a64'],
                normal: '#5ea897', inconclusive: '#d4a33d', abnormal: '#c96f6f'
            };

            // 1. CHART TREN
            const ctxTren = document.getElementById('chartTren');
            new Chart(ctxTren, {
                type: 'bar',
                data: {
                    labels: trenData.map(item => item.tahun),
                    datasets: [{
                        label: 'Total Pasien',
                        data: trenData.map(item => item.total),
                        backgroundColor: trenData.map(item => item.tahun == tahunAktif ? palette.active : palette.trend),
                        borderRadius: 6, barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    onClick: (event, elements) => {
                        event.native.stopPropagation(); // Agar klik bar tidak menutup panel analisis
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const tahun = trenData[index].tahun;
                            window.location.href = "{{ route('dashboard.index') }}?tahun=" + tahun;
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        datalabels: { display: false },
                        tooltip: { 
                            callbacks: { label: (ctx) => 'Pasien: ' + formatNumber(ctx.raw) } 
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#e2e8f0' }, ticks: { callback: (val) => formatNumber(val) } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. CHART DEMOGRAFI
            new Chart(document.getElementById('chartDemografi'), {
                type: 'bar',
                data: {
                    labels: demografiData.map(item => item.Age_Group),
                    datasets: [
                        { label: 'Perempuan', data: demografiData.map(item => item.female), backgroundColor: palette.female, borderRadius: 4 },
                        { label: 'Laki-laki', data: demografiData.map(item => item.male), backgroundColor: palette.male, borderRadius: 4 }
                    ]
                },
                options: {
                    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'start', labels: { usePointStyle: true, boxWidth: 8 } },
                        datalabels: { display: false }
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false }, ticks: { callback: (val) => formatNumber(val) } },
                        y: { stacked: true, grid: { display: false } }
                    }
                }
            });

            // 3. CHART GOLONGAN DARAH
            new Chart(document.getElementById('chartDarah'), {
                type: 'pie',
                data: {
                    labels: darahData.map(item => item.Blood_Type),
                    datasets: [{
                        data: darahData.map(item => item.total),
                        backgroundColor: palette.blood,
                        borderWidth: 2, borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, layout: { padding: 20 },
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            color: '#475569', anchor: 'end', align: 'end', offset: 5,
                            font: { weight: '600', size: 11 },
                            formatter: (value, ctx) => {
                                if (window.innerWidth < 400 && value < 100) return "";
                                return formatNumber(value) + "\n" + ctx.chart.data.labels[ctx.dataIndex];
                            }
                        }
                    }
                }
            });

            // 4. CHART KONDISI MEDIS
            const dataChart4 = @json($hasilTes);
            if (dataChart4 && dataChart4.length > 0) {
                new Chart(document.getElementById('chartKondisi'), {
                    type: 'bar',
                    data: {
                        labels: dataChart4.map(item => item.Medical_Condition || "Tanpa Nama"),
                        datasets: [
                            { label: 'Normal', data: dataChart4.map(item => Number(item.normal || 0)), backgroundColor: '#5ea897', borderRadius: 4 },
                            { label: 'Inconclusive', data: dataChart4.map(item => Number(item.inconclusive || 0)), backgroundColor: '#d4a33d', borderRadius: 4 },
                            { label: 'Abnormal', data: dataChart4.map(item => Number(item.abnormal || 0)), backgroundColor: '#c96f6f', borderRadius: 4 }
                        ]
                    },
                    options: {
                        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            datalabels: {
                                color: '#475569', anchor: 'end', align: 'end', offset: 4,
                                font: { size: 10, weight: '600' },
                                formatter: (value) => value > 0 ? value : '',
                                display: function () { return window.innerWidth > 600; }
                            }
                        },
                        scales: {
                            x: { stacked: false, grid: { display: false } },
                            y: { stacked: false, grid: { display: false } }
                        }
                    }
                });
            }
        });
    </script>
@endsection