@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 bg-light" style="min-height: 100vh;">
    
    <div class="card border-0 shadow-sm mb-4 mx-2" style="border-radius: 20px; background: white;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h1 class="h3 mb-1 text-gray-900 font-weight-extrabold tracking-tight">Financial Billing Analysis</h1>
                    <p class="text-muted mb-0 font-weight-medium">Monitor hospital revenue trends and costs effectively.</p>
                    
                    @if(request('tahun'))
                        <div class="mt-3">
                            <span class="badge badge-light text-dark border px-3 py-2 shadow-sm animate__animated animate__fadeIn" style="border-radius: 10px; font-size: 0.85rem;">
                                <i class="fas fa-filter text-danger mr-2"></i>Status: <strong>Terfilter Tahun {{ request('tahun') }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                <div class="col-md-5 text-md-right mt-3 mt-md-0">
                    <div class="d-flex flex-wrap justify-content-md-end align-items-center" style="gap: 12px;">
                        @if(request('tahun'))
                            <a href="{{ route('billing.index') }}" 
                            class="btn shadow d-flex align-items-center transition-all hover-lift" 
                            style="border-radius: 12px; font-weight: 700; padding: 10px 25px; border: none; background-color: #dc3545 !important; color: white !important;">
                                Reset Filter
                            </a>
                        @endif

                        <a href="{{ url('/') }}" class="text-decoration-none">
                            <button type="button" class="btn shadow px-4 py-2 border-0 d-flex align-items-center transition-all hover-lift" 
                                    style="border-radius: 12px; background-color: #1cc88a; color: white; padding: 10px 22px;">
                                <i class="fas fa-arrow-left fa-sm text-white mr-3"></i> 
                                <span class="font-weight-bold">Back to Dashboard</span>
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row px-2">
        <div class="col-12 mb-5">
            <div class="card border-0 shadow-sm modern-card" onclick="toggleAnalysis('analysis-revenue')" style="cursor: pointer;">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success-light mr-3">
                            <i class="fas fa-chart-line text-success"></i>
                        </div>
                        <div>
                            <h5 class="m-0 font-weight-bold text-dark">Annual Revenue Trend</h5>
                            <small class="text-muted">Klik card ini untuk melihat analisis data</small>
                        </div>
                    </div>
                    <i class="fas fa-plus-circle text-success transition-all" id="icon-analysis-revenue"></i>
                </div>
                <div class="card-body p-4">
                    <div style="height: 400px; position: relative;">
                        <canvas id="lineChartYear"></canvas>
                    </div>

                    <div id="analysis-revenue" class="analysis-content mt-4" style="display: none;">
                        <div class="p-4 rounded-lg border-left-success" style="background-color: #f8fffb; border-left: 5px solid #1cc88a;">
                            <h6 class="font-weight-bold text-success mb-2">💡 Analisis Pendapatan:</h6>
                            <p class="text-sm text-dark mb-0">
                                Berdasarkan grafik tren tahunan, stabilitas finansial rumah sakit terjaga dengan baik dari 2019-2023. 
                                <strong>Penting:</strong> Penurunan angka pada tahun 2024 disebabkan karena data laporan baru tersedia hingga <b>Mei 2024</b>. 
                                Secara rata-rata bulanan, arus kas tetap konsisten dengan performa tahun lalu.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mb-5">
            <div class="card border-0 shadow-sm modern-card" onclick="toggleAnalysis('analysis-condition')" style="cursor: pointer;">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success-light mr-3">
                            <i class="fas fa-hand-holding-usd text-success"></i>
                        </div>
                        <h5 class="m-0 font-weight-bold text-dark">
                            Billing by Medical Condition 
                            <span class="text-success ml-1">{{ request('tahun') ? '(Tahun '.request('tahun').')' : '(Total Keseluruhan)' }}</span>
                        </h5>
                    </div>
                    <i class="fas fa-plus-circle text-success transition-all" id="icon-analysis-condition"></i>
                </div>
                <div class="card-body p-4">
                    <div style="height: 400px; position: relative;">
                        <canvas id="barChartCondition"></canvas>
                    </div>

                    <div id="analysis-condition" class="analysis-content mt-4" style="display: none;">
                        <div class="p-4 rounded-lg border-left-warning" style="background-color: #fffaf0; border-left: 5px solid #f59e0b;">
                            <h6 class="font-weight-bold text-warning mb-2">💡 Analisis Biaya Medis:</h6>
                            <p class="text-sm text-dark mb-0">
                                Distribusi billing menunjukkan bahwa kondisi medis seperti <strong>Diabetes</strong> dan <strong>Hypertension</strong> 
                                seringkali menghasilkan total tagihan yang lebih tinggi. 
                                Hal ini mengindikasikan bahwa penyakit kronis memerlukan sumber daya medis dan masa perawatan yang lebih intensif dibandingkan kondisi lainnya.
                                {{ request('tahun') ? 'Pada tahun '.request('tahun').', proporsi biaya ini tetap menjadi kontributor utama pendapatan.' : '' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
    
    body { font-family: 'Inter', sans-serif; }
    .font-weight-extrabold { font-weight: 800; }
    .tracking-tight { letter-spacing: -0.025em; }

    .modern-card {
        border-radius: 24px;
        background: #ffffff;
        transition: all 0.3s ease;
    }
    
    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }

    .icon-box {
        width: 48px; height: 48px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 14px;
    }

    .bg-success-light { background-color: rgba(28, 200, 138, 0.12); }
    .transition-all { transition: all 0.3s ease; }
    
    .hover-lift:hover { 
        transform: translateY(-2px); 
        filter: brightness(1.05); 
    }

    .analysis-content {
        animation: slideDown 0.4s ease-out;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .text-sm { font-size: 0.9rem; line-height: 1.6; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // FUNGSI TOGGLE ANALISIS
    function toggleAnalysis(id) {
        const content = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        
        if (content.style.display === "none") {
            content.style.display = "block";
            icon.classList.replace('fa-plus-circle', 'fa-minus-circle');
            icon.style.color = "#e74a3b"; // Change to red when open
        } else {
            content.style.display = "none";
            icon.classList.replace('fa-minus-circle', 'fa-plus-circle');
            icon.style.color = "#1cc88a";
        }
    }

    const formatCurrency = (value) => {
        if (value >= 1000000000) return '$' + (value / 1000000000).toFixed(2) + ' B';
        if (value >= 1000000) return '$' + (value / 1000000).toFixed(1) + ' M';
        return '$' + value.toLocaleString();
    };

    const successGreen = '#1cc88a'; 
    const filteredOrange = '#f59e0b';
    const currentYear = "{{ request('tahun') }}";

    // LINE CHART - ANNUAL REVENUE
    const billingData = {!! json_encode($billingByYear) !!};
    new Chart(document.getElementById('lineChartYear'), {
        type: 'line',
        data: {
            labels: billingData.map(d => d.tahun),
            datasets: [{
                data: billingData.map(d => d.total),
                borderColor: successGreen,
                borderWidth: 5,
                tension: 0.4,
                pointBackgroundColor: billingData.map(d => d.tahun == currentYear ? filteredOrange : '#ffffff'),
                pointBorderColor: billingData.map(d => d.tahun == currentYear ? filteredOrange : successGreen),
                pointBorderWidth: 4,
                pointRadius: billingData.map(d => d.tahun == currentYear ? 10 : 7),
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: (e, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const year = billingData[index].tahun;
                    window.location.href = "{{ route('billing.index') }}?tahun=" + year;
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (ctx) => ' Revenue: ' + formatCurrency(ctx.parsed.y) } }
            },
            scales: {
                y: { ticks: { callback: formatCurrency }, grid: { borderDash: [6, 6] } },
                x: { grid: { display: false } }
            }
        }
    });

    // BAR CHART - CONDITION
    new Chart(document.getElementById('barChartCondition'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($billingByCondition->pluck('Medical_Condition')) !!},
            datasets: [{
                data: {!! json_encode($billingByCondition->pluck('total')) !!},
                backgroundColor: currentYear ? filteredOrange : successGreen,
                borderRadius: 12,
                barThickness: 32
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (ctx) => ' Total: ' + formatCurrency(ctx.parsed.x) } }
            },
            scales: {
                x: { ticks: { callback: formatCurrency }, grid: { borderDash: [6, 6] } },
                y: { grid: { display: false } }
            }
        }
    });
</script>
@endsection