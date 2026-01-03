@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 bg-light" style="min-height: 100vh;">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-5 px-3">
        <div>
            <h1 class="h2 mb-1 text-gray-900 font-weight-extrabold tracking-tight">Financial Billing Analysis</h1>
            <p class="text-muted mb-0 font-weight-medium">Monitor hospital revenue trends and medical condition costs effectively.</p>
        </div>
        
        <a href="{{ url('/') }}" class="text-decoration-none">
            <button type="button" class="btn shadow px-4 py-2 border-0 d-flex align-items-center transition-all hover-lift" 
                    style="border-radius: 12px; background-color: #1cc88a; color: white;">
                <i class="fas fa-arrow-left fa-sm text-white mr-3"></i> 
                <span class="font-weight-bold" style="font-size: 0.9rem;">Back to Dashboard</span>
            </button>
        </a>
    </div>

    <div class="row px-2">
        
        <div class="col-12 mb-5">
            <div class="card border-0 shadow-sm modern-card">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center">
                    <div class="icon-box bg-success-light mr-3">
                        <i class="fas fa-chart-line text-success"></i>
                    </div>
                    <h5 class="m-0 font-weight-bold text-dark">Annual Revenue Trend (2019 - 2024)</h5>
                </div>
                <div class="card-body p-4">
                    <div style="height: 400px; position: relative;">
                        <canvas id="lineChartYear"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mb-5">
            <div class="card border-0 shadow-sm modern-card">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success-light mr-3">
                            <i class="fas fa-hand-holding-usd text-success"></i>
                        </div>
                        <h5 class="m-0 font-weight-bold text-dark">Total Billing by Medical Condition</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div style="height: 400px; position: relative;">
                        <canvas id="barChartCondition"></canvas>
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

    /* Card Styling & Hover Effect */
    .modern-card {
        border-radius: 24px;
        background: #ffffff;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
    }
    
    .modern-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03) !important;
    }

    .icon-box {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
    }

    .bg-success-light { background-color: rgba(28, 200, 138, 0.12); }

    .transition-all { transition: all 0.2s ease-in-out; }
    
    .hover-lift:hover {
        transform: translateY(-2px);
        filter: brightness(1.1);
        box-shadow: 0 4px 12px rgba(28, 200, 138, 0.3) !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const formatCurrency = (value) => {
        if (value >= 1000000000000) return '$' + (value / 1000000000000).toFixed(1) + 'T';
        if (value >= 1000000000) return '$' + (value / 1000000000).toFixed(1) + 'B';
        if (value >= 1000000) return '$' + (value / 1000000).toFixed(1) + 'M';
        return '$' + value.toLocaleString();
    };

    const successGreen = '#1cc88a'; 
    const darkText = '#1a202c';

    // 1. Bar Chart - Total Billing
    new Chart(document.getElementById('barChartCondition'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($billingByCondition->pluck('Medical_Condition')) !!},
            datasets: [{
                label: 'Total Revenue',
                data: {!! json_encode($billingByCondition->pluck('total')) !!},
                backgroundColor: successGreen,
                borderRadius: 12,
                barThickness: 32,
                hoverBackgroundColor: '#17a673'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a202c',
                    padding: 15,
                    cornerRadius: 10,
                    callbacks: { label: (ctx) => ' Revenue: ' + formatCurrency(ctx.parsed.x) }
                }
            },
            scales: {
                x: { 
                    ticks: { color: '#718096', callback: formatCurrency },
                    grid: { borderDash: [6, 6], color: '#edf2f7', drawBorder: false }
                },
                y: { 
                    ticks: { color: darkText, font: { weight: '600' } },
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });

    // 2. Line Chart - Annual Trend
    new Chart(document.getElementById('lineChartYear'), {
        type: 'line',
        data: {
            labels: {!! json_encode($billingByYear->pluck('tahun')) !!},
            datasets: [{
                data: {!! json_encode($billingByYear->pluck('total')) !!},
                borderColor: successGreen,
                borderWidth: 5,
                tension: 0.4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: successGreen,
                pointBorderWidth: 4,
                pointRadius: 7,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a202c',
                    padding: 15,
                    cornerRadius: 10,
                    callbacks: { label: (ctx) => ' Total Revenue: ' + formatCurrency(ctx.parsed.y) }
                }
            },
            scales: {
                y: { 
                    ticks: { color: '#718096', callback: formatCurrency },
                    grid: { borderDash: [6, 6], color: '#edf2f7', drawBorder: false }
                },
                x: { 
                    ticks: { color: darkText, font: { weight: '600' } },
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });
</script>
@endsection