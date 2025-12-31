@extends('layouts.app')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
        .card { 
            background: white; 
            padding: 1.5rem; 
            border-radius: 12px; 
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); 
            border: 1px solid #e2e8f0; 
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        canvas { width: 100% !important; height: 100% !important; }
    </style>

    <div class="p-4 md:p-8 text-slate-700 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-6 md:space-y-8">
            
            <div class="flex flex-col">
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">Dashboard Analisis Kesehatan</h1>
                <p class="text-sm md:text-base text-slate-500 mt-1">Laporan visual data pasien dan kondisi medis periode 2019-2024.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500 flex flex-col justify-center">
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-wider">Total Patients</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($tren->sum('total')) }}</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500 flex flex-col justify-center">
                    <p class="text-xs font-bold text-green-500 uppercase tracking-wider">Total Billing Amount</p>
                    <p class="text-2xl font-bold text-slate-800">
                        ${{ number_format($totalBilling / 1000000, 2) }} Million
                    </p>
                </div>
            </div>

            <div class="w-full">
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 text-slate-700">Tren Pasien Tahunan</h2>
                    <div class="relative w-full h-[300px] md:h-[400px]">
                        <canvas id="chartTren"></canvas>
                    </div>
                </div>
            </div>

              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 text-slate-700">Demografi Gender & Umur</h2>
                    <div class="relative w-full h-[300px] md:h-[350px]">
                        <canvas id="chartDemografi"></canvas>
                    </div>
                </div>

                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 text-slate-700">Proporsi Golongan Darah</h2>
                    <div class="relative w-full h-[300px] md:h-[350px] flex justify-center items-center">
                        <canvas id="chartDarah"></canvas>
                    </div>
                </div>
            </div>

            <div class="w-full"> 
                <div class="card">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <h2 class="text-lg font-semibold text-slate-700">Hasil Tes Berdasarkan Kondisi Medis</h2>
                        
                        <div class="flex flex-wrap gap-3 md:gap-6 text-sm font-medium">
                            <div class="flex items-center"><span class="w-3 h-3 bg-[#5ea897] rounded-full mr-2"></span>Normal</div>
                            <div class="flex items-center"><span class="w-3 h-3 bg-[#d4a33d] rounded-full mr-2"></span>Inconclusive</div>
                            <div class="flex items-center"><span class="w-3 h-3 bg-[#c96f6f] rounded-full mr-2"></span>Abnormal</div>
                        </div>
                    </div>
                    
                    <div class="relative w-full h-[500px] md:h-[650px]">
                        <canvas id="chartKondisi"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.register(ChartDataLabels);

            // ============================================
            // 1. DATA DARI CONTROLLER
            // ============================================
            const trenData      = @json($tren);
            const demografiData = @json($demografi);
            const darahData     = @json($darah); 

            // Helper format angka
            const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(num);

            const palette = {
                trend: '#6366f1', 
                female: '#f9a8d4', male: '#7dd3fc',   
                blood: ['#3b5998', '#607d8b', '#8e44ad', '#00acc1', '#795548', '#546e7a', '#9575cd', '#455a64'],
                normal: '#5ea897', 
                inconclusive: '#d4a33d', 
                abnormal: '#c96f6f'
            };

            // --- CHART 1: TREN PASIEN ---
            new Chart(document.getElementById('chartTren'), {
                type: 'bar', 
                data: { 
                    labels: trenData.map(item => item.tahun), 
                    datasets: [{ 
                        label: 'Total Pasien',
                        data: trenData.map(item => item.total), 
                        backgroundColor: palette.trend, 
                        borderRadius: 6, barPercentage: 0.6 
                    }] 
                },
                options: { 
                    responsive: true, maintainAspectRatio: false, 
                    plugins: { 
                        legend: { display: false }, 
                        datalabels: { display: false },
                        tooltip: { callbacks: { label: (ctx) => 'Total: ' + formatNumber(ctx.raw) } }
                    }, 
                    scales: { 
                        y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#e2e8f0' }, ticks: { callback: (val) => formatNumber(val) } }, 
                        x: { grid: { display: false } } 
                    } 
                }
            });

            // --- CHART 2: DEMOGRAFI ---
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
                        datalabels: { display: false },
                        tooltip: { callbacks: { label: (ctx) => ctx.dataset.label + ': ' + formatNumber(ctx.raw) } }
                    }, 
                    scales: { 
                        x: { stacked: true, grid: { display: false }, ticks: { callback: (val) => formatNumber(val) } }, 
                        y: { stacked: true, grid: { display: false } } 
                    } 
                }
            });

            // --- CHART 3: GOLONGAN DARAH ---
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
                                // Sembunyikan label jika layar terlalu kecil atau nilai 0 agar tidak berantakan
                                if(window.innerWidth < 400 && value < 100) return ""; 
                                return formatNumber(value) + "\n" + ctx.chart.data.labels[ctx.dataIndex];
                            }
                        },
                        tooltip: { callbacks: { label: (ctx) => ctx.label + ': ' + formatNumber(ctx.raw) } }
                    } 
                }
            });

            // ============================================
            // --- CHART 4: KONDISI MEDIS (SUDAH DINAMIS) ---
            // ============================================
            // ============================================
            // --- CHART 4: KONDISI MEDIS (VERSI FINAL & DEBUG) ---
            // ============================================

            // 1. Ambil Data
            const dataChart4 = @json($hasilTes); 
            
            // 2. CEK ISI DATA (Akan muncul Popup di layar)
            if (dataChart4.length > 0) {
                // Tampilkan isi data baris pertama untuk kita intip nama variabelnya
                // alert("ISI DATA DARI CONTROLLER:\n" + JSON.stringify(dataChart4[0], null, 2));
                console.log("Data Lengkap Chart 4:", dataChart4);
            } else {
                console.error("Data Chart 4 Kosong/Null dari Controller");
            }

            if (dataChart4 && dataChart4.length > 0) {
                
                // 3. MAPPING PINTAR (Cek Huruf Besar & Kecil otomatis)
                // Kita pakai "||" (ATAU) biar dia cari mana yang ada isinya
                
                const labelPenyakit = dataChart4.map(item => item.Medical_Condition || item.medical_condition || "Tanpa Nama");
                
                const valNormal = dataChart4.map(item => 
                    Number(item.normal || item.Normal || item.NORMAL || 0)
                );
                
                const valIncon = dataChart4.map(item => 
                    Number(item.inconclusive || item.Inconclusive || item.INCONCLUSIVE || 0)
                );
                
                const valAbnormal = dataChart4.map(item => 
                    Number(item.abnormal || item.Abnormal || item.ABNORMAL || 0)
                );

                // 4. Render Chart
                const ctx4 = document.getElementById('chartKondisi');
                
                if (ctx4) {
                    new Chart(ctx4, {
                        type: 'bar',
                        data: {
                            labels: labelPenyakit,
                            datasets: [
                                {
                                    label: 'Normal',
                                    data: valNormal,
                                    backgroundColor: '#5ea897', 
                                    barPercentage: 0.7, categoryPercentage: 0.8, borderRadius: 4
                                },
                                {
                                    label: 'Inconclusive',
                                    data: valIncon,
                                    backgroundColor: '#d4a33d', 
                                    barPercentage: 0.7, categoryPercentage: 0.8, borderRadius: 4
                                },
                                {
                                    label: 'Abnormal',
                                    data: valAbnormal,
                                    backgroundColor: '#c96f6f', 
                                    barPercentage: 0.7, categoryPercentage: 0.8, borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            indexAxis: 'y', 
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                datalabels: {
                                    color: '#475569', anchor: 'end', align: 'end', offset: 4,
                                    font: { size: 10, weight: '600' },
                                    formatter: (value) => value > 0 ? value : '', // Sembunyikan 0
                                    display: function(context) { return window.innerWidth > 600; }
                                }
                            },
                            scales: {
                                x: { stacked: false, grid: { display: false } },
                                y: { stacked: false, grid: { display: false } }
                            }
                        }
                    });
                }
            }
           
        });
    </script>
@endsection