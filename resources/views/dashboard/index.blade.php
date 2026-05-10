@extends('layouts.template')

@section('own_style')
    <style>
        .size-column {
            display: flex;
            align-items: stretch;
        }

        .size-column>div {
            display: flex;
        }

        .size-column .card {
            width: 100%;
        }

        #revenueChart {
            height: 250px !important;
        }

        .revenue-card {
            border-radius: 24px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            position: relative;
            height: 360px;
            min-height: 360px;
            max-height: 360px;
            overflow: hidden;
        }

        .revenue-icon-bg {
            position: absolute;
            right: -10px;
            bottom: -20px;
            font-size: 140px;
            opacity: .08;
            color: #fff;
        }

        .revenue-footer {
            border-top: 1px solid rgba(255, 255, 255, .12);
            padding-top: 18px;
        }

        .mini-info {
            background: rgba(255, 255, 255, .08);
            padding: 10px 16px;
            border-radius: 14px;
            backdrop-filter: blur(10px);
        }
    </style>
@endsection

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @else
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6">
                        <h4>{{ $pageTitle }}</h4>
                    </div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">
                                    <svg class="stroke-icon">
                                        <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#stroke-home') }}">
                                        </use>
                                    </svg></a></li>
                            <li class="breadcrumb-item">{{ $pageTitle }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row size-column">
                <div class="col-lg-8 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Pendapatan Chart</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card boost-up-card overflow-hidden revenue-card">
                        <div class="p-4 position-relative">

                            <div class="revenue-icon-bg">
                                <i class="fa fa-money"></i>
                            </div>

                            <span class="badge bg-success mb-3">
                                Pendapatan Hari Ini
                            </span>

                            <h2 class="text-white f-w-700 mb-1">
                                Rp {{ number_format($revenue['today'], 0, ',', '.') }}
                            </h2>

                            <p class="text-white-50 f-14 mb-4">
                                Total pendapatan transaksi hari ini
                            </p>

                            <div class="d-flex align-items-center gap-3 flex-wrap mb-4">

                                <div class="mini-info">
                                    <small class="text-white-50 d-block">
                                        Order Hari Ini
                                    </small>
                                    <span class="text-white f-w-600">
                                        {{ $orders['today'] }}
                                    </span>
                                </div>

                            </div>

                            <div class="d-flex justify-content-between align-items-center revenue-footer">

                                <div>
                                    <small class="text-white-50">
                                        Dibanding kemarin
                                    </small>

                                    <div class="text-warning f-w-700">
                                        <i class="fa fa-arrow-up me-1"></i>
                                        {{ $revenue['growthRevenue'] ?? 0 }}%
                                    </div>
                                </div>

                                <a href="#">
                                    <button class="btn btn-pill btn-outline-light-2x b-r-8" type="button">
                                        Detail
                                    </button>
                                </a>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row size-column">
                <div class="card">
                    <div class="card-header">
                        <h4>Revenue</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-sm-6">
                                <div class="card o-hidden small-widget">
                                    <div class="card-body total-project border-b-primary border-2"><span
                                            class="f-light f-w-500 f-14">Total</span>
                                        <div class="project-details">
                                            <div class="project-counter">
                                                <h2 class="f-w-600" style="font-size:18px">Rp
                                                    {{ number_format($revenue['total'], 0, ',', '.') }}
                                                </h2>
                                                <span class="f-12 f-w-400">(Total)</span>
                                            </div>
                                            <div class="product-sub bg-primary-light">
                                                <svg class="invoice-icon">
                                                    <use
                                                        href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#color-swatch') }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="bubbles">
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="card o-hidden small-widget">
                                    <div class="card-body total-Progress border-b-warning border-2"> <span
                                            class="f-light f-w-500 f-14">Tahun Ini</span>
                                        <div class="project-details">
                                            <div class="project-counter">
                                                <h2 class="f-w-600" style="font-size:18px">Rp
                                                    {{ number_format($revenue['year'], 0, ',', '.') }}
                                                </h2>
                                                <span class="f-12 f-w-400">(Tahun Ini)</span>
                                            </div>
                                            <div class="product-sub bg-warning-light">
                                                <svg class="invoice-icon">
                                                    <use
                                                        href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#tick-circle') }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="bubbles">
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="card o-hidden small-widget">
                                    <div class="card-body total-Complete border-b-secondary border-2"><span
                                            class="f-light f-w-500 f-14">Bulan Ini</span>
                                        <div class="project-details">
                                            <div class="project-counter">
                                                <h2 class="f-w-600" style="font-size:18px">Rp
                                                    {{ number_format($revenue['month'], 0, ',', '.') }}
                                                </h2>
                                                <span class="f-12 f-w-400">(Bulan Ini)</span>
                                            </div>
                                            <div class="product-sub bg-secondary-light">
                                                <svg class="invoice-icon">
                                                    <use
                                                        href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#add-square') }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="bubbles">
                                            <li class="bubble"> </li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"> </li>
                                            <li class="bubble"></li>
                                            <li class="bubble"> </li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"> </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="card o-hidden small-widget">
                                    <div class="card-body total-upcoming"><span class="f-light f-w-500 f-14">Hari
                                            Ini</span>
                                        <div class="project-details">
                                            <div class="project-counter">
                                                <h2 class="f-w-600" style="font-size:18px">Rp
                                                    {{ number_format($revenue['today'], 0, ',', '.') }}
                                                </h2><span class="f-12 f-w-400">(Hari Ini)</span>
                                            </div>
                                            <div class="product-sub bg-light-light">
                                                <svg class="invoice-icon">
                                                    <use
                                                        href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#edit-2') }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="bubbles">
                                            <li class="bubble"> </li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row size-column">
                <div class="card">
                    <div class="card-header">
                        <h4>Order</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-sm-6">
                                <div class="card o-hidden small-widget">
                                    <div class="card-body total-project border-b-primary border-2"><span
                                            class="f-light f-w-500 f-14">Total</span>
                                        <div class="project-details">
                                            <div class="project-counter">
                                                <h2 class="f-w-600">{{ $orders['total'] }}</h2>
                                                <p class="f-12 f-w-400">(Order)</p>
                                            </div>
                                            <div class="product-sub bg-primary-light">
                                                <svg class="invoice-icon">
                                                    <use
                                                        href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#color-swatch') }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="bubbles">
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="card o-hidden small-widget">
                                    <div class="card-body total-Progress border-b-warning border-2"> <span
                                            class="f-light f-w-500 f-14">Tahun Ini</span>
                                        <div class="project-details">
                                            <div class="project-counter">
                                                <h2 class="f-w-600">{{ $orders['year'] }}
                                                </h2>
                                                <p class="f-12 f-w-400">(Order)</p>
                                            </div>
                                            <div class="product-sub bg-warning-light">
                                                <svg class="invoice-icon">
                                                    <use
                                                        href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#tick-circle') }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="bubbles">
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="card o-hidden small-widget">
                                    <div class="card-body total-Complete border-b-secondary border-2"><span
                                            class="f-light f-w-500 f-14">Bulan Ini</span>
                                        <div class="project-details">
                                            <div class="project-counter">
                                                <h2 class="f-w-600">{{ $orders['month'] }}
                                                </h2>
                                                <p class="f-12 f-w-400">(Order)</p>
                                            </div>
                                            <div class="product-sub bg-secondary-light">
                                                <svg class="invoice-icon">
                                                    <use
                                                        href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#add-square') }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="bubbles">
                                            <li class="bubble"> </li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"> </li>
                                            <li class="bubble"></li>
                                            <li class="bubble"> </li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"> </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="card o-hidden small-widget">
                                    <div class="card-body total-upcoming"><span class="f-light f-w-500 f-14">Hari
                                            Ini</span>
                                        <div class="project-details">
                                            <div class="project-counter">
                                                <h2 class="f-w-600">{{ $orders['today'] }}
                                                </h2>
                                                <p class="f-12 f-w-400">(Order)</p>
                                            </div>
                                            <div class="product-sub bg-light-light">
                                                <svg class="invoice-icon">
                                                    <use
                                                        href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#edit-2') }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="bubbles">
                                            <li class="bubble"> </li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                            <li class="bubble"></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row size-column">
                <div class="col-12 col-lg-6">
                    <div class="card height-equal">
                        <div class="card-header card-no-border total-revenue">
                            <h4>Best Seller Menu</h4>
                            <a href="#">View All</a>
                        </div>
                        <div class="card-body pt-0">
                            <div class="top-product-card">
                                <ul>
                                    @forelse($bestSellerMenus as $menu)
                                        <li class="d-flex top-product gap-2">
                                            <div>
                                                <img class="img-fluid product-img"
                                                    src="{{ optional($menu->menu->fotoMenus->first())->foto_path
                                                        ? asset('storage/' . $menu->menu->fotoMenus->first()->foto_path)
                                                        : asset('no-image.png') }}"
                                                    alt="{{ $menu->nama_menu }}">
                                            </div>
                                            <div class="w-100 d-flex justify-content-between align-items-center">
                                                <div class="product-details">
                                                    <div>
                                                        <span class="badge rounded-pill badge-light text-dark">
                                                            TOP MENU
                                                        </span>
                                                    </div>
                                                    <a class="f-10 f-w-500 line-clamp" href="#">
                                                        {{ $menu->nama_menu }}
                                                    </a>
                                                    <span class="f-10 f-w-500 txt-primary">
                                                        Rp {{ number_format($menu->total_income, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                                <div class="product-items">
                                                    <div class="common-space gap-1">
                                                        <span class="f-10 f-w-500 f-light">
                                                            QTY :
                                                        </span>
                                                        <span class="f-10 f-w-500">
                                                            {{ $menu->total_qty }}
                                                        </span>
                                                    </div>
                                                    <div class="common-space gap-1">
                                                        <span class="f-10 f-w-500 f-light">
                                                            Revenue :
                                                        </span>
                                                        <span class="f-10 f-w-500">
                                                            Rp {{ number_format($menu->total_income, 0, ',', '.') }}
                                                        </span>
                                                    </div>
                                                    <div class="common-space gap-1">
                                                        <span class="f-10 f-w-500 f-light">
                                                            Avg :
                                                        </span>
                                                        <span class="f-10 f-w-500">
                                                            Rp
                                                            {{ number_format($menu->total_income / max($menu->total_qty, 1), 0, ',', '.') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li>
                                            <div class="alert alert-warning mb-0">
                                                Belum ada data penjualan menu
                                            </div>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card height-equal">
                        <div class="card-header total-revenue card-no-border">
                            <h4>Latest Orders</h4>
                            <div class="d-flex align-items-center gap-2">
                                <a href="/daftar-order" class="btn btn-primary btn-sm refresh-latest-orders">
                                    Detail
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-order table-responsive custom-scrollbar">
                                <table class="latest-orders w-100">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($latestOrders as $order)
                                            <tr>
                                                <td>
                                                    <div class="product-name">
                                                        <div class="product-sub">
                                                            <a class="f-14 f-w-500" href="#">
                                                                {{ $order->order_id }}
                                                            </a>
                                                            <span class="f-light f-14 f-w-500 d-block">
                                                                {{ $order->created_at->format('d M Y H:i') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="product-sub">
                                                        <a class="f-14 f-w-500" href="#">
                                                            {{ $order->customer_name ?? 'Customer Umum' }}
                                                        </a>
                                                        <span class="f-light f-14 f-w-500 d-block">
                                                            {{ $order->meja->nama_meja ?? 'Tanpa Meja' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="product-sub">
                                                        <a class="f-14 f-w-500" href="#">
                                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                                        </a>
                                                        <span class="f-light f-14 f-w-500 d-block">
                                                            {{ $order->items->sum('qty') }} Item
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $badge = match ($order->status) {
                                                            'paid' => 'success',
                                                            'pending' => 'warning',
                                                            'cancelled' => 'danger',
                                                            'expired' => 'secondary',
                                                            default => 'primary',
                                                        };
                                                    @endphp
                                                    <div
                                                        class="badge-light-{{ $badge }} product-sub badge rounded-pill">
                                                        <span>
                                                            {{ ucfirst($order->status) }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="product-sub">
                                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#modalOrder{{ $order->id }}">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">
                                                    <div class="alert alert-warning mb-0 text-center">
                                                        Belum ada transaksi
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row size-column">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <i class="fa fa-exclamation-circle me-2"></i>
                        {{ session('error') }}

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection

@section('own_script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const revenueChart = @json($revenue_chart);
        const labels = revenueChart.map(item => item.month);
        const totals = revenueChart.map(item => item.total);
        const ctx = document.getElementById('revenueChart');

        // Setting tinggi canvas lebih tinggi
        ctx.style.height = '250px';
        ctx.height = 250;

        // Fungsi untuk membuat gradient fill yang cantik
        const createGradient = (ctx) => {
            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 450);
            gradient.addColorStop(0, 'rgba(67, 97, 238, 0.35)');
            gradient.addColorStop(0.4, 'rgba(67, 97, 238, 0.12)');
            gradient.addColorStop(1, 'rgba(67, 97, 238, 0.01)');
            return gradient;
        };

        const gradientFill = createGradient(ctx);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: totals,
                    borderWidth: 3.5,
                    borderColor: '#4361ee',
                    borderDash: [],
                    borderJoin: 'round',
                    backgroundColor: gradientFill,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 9,
                    pointBorderWidth: 2.5,
                    pointBorderColor: '#ffffff',
                    pointBackgroundColor: '#4361ee',
                    pointStyle: 'circle',
                    shadowOffsetX: 0,
                    shadowOffsetY: 4,
                    shadowBlur: 8,
                    shadowColor: 'rgba(67, 97, 238, 0.25)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'center',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 10,
                            boxHeight: 10,
                            font: {
                                family: "'Inter', 'Segoe UI', sans-serif",
                                size: 12,
                                weight: '600'
                            },
                            color: '#1e293b',
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.96)',
                        titleColor: '#f1f5f9',
                        titleFont: {
                            family: "'Inter', sans-serif",
                            size: 13,
                            weight: '600'
                        },
                        bodyColor: '#cbd5e1',
                        bodyFont: {
                            family: "'Inter', sans-serif",
                            size: 12
                        },
                        footerColor: '#94a3b8',
                        padding: 12,
                        cornerRadius: 12,
                        borderColor: '#4361ee',
                        borderWidth: 1,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                return label + ': ' + value;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 500000,
                            callback: function(value) {
                                if (value >= 1000000000) {
                                    return 'Rp ' + (value / 1000000000).toFixed(1) + 'M';
                                } else if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(0) + 'JT';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'RB';
                                }
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            },
                            font: {
                                family: "'Inter', sans-serif",
                                size: 11,
                                weight: '500'
                            },
                            color: '#64748b',
                            padding: 10,
                            maxTicksLimit: 8
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.06)',
                            drawBorder: false,
                            lineWidth: 1,
                            tickLength: 0,
                            drawTicks: false
                        },
                        border: {
                            dash: [4, 4],
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'NOMINAL (Rp)',
                            font: {
                                family: "'Inter', sans-serif",
                                size: 10,
                                weight: '600'
                            },
                            color: '#94a3b8',
                            padding: {
                                bottom: 10
                            }
                        },
                        afterBuildTicks: function(axis) {
                            // Memastikan ticks kelipatan 500.000
                            const ticks = axis.ticks;
                            const maxValue = Math.max(...totals, 0);
                            // Tentukan max tick yang merupakan kelipatan 500.000 ke atas
                            let maxTick = Math.ceil(maxValue / 500000) * 500000;
                            // Buat ulang ticks dengan step 500.000 dari 0 sampai maxTick
                            const newTicks = [];
                            for (let i = 0; i <= maxTick / 500000; i++) {
                                newTicks.push(i * 500000);
                            }
                            axis.ticks = newTicks.map(value => ({
                                value
                            }));
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                            drawTicks: false
                        },
                        ticks: {
                            font: {
                                family: "'Inter', sans-serif",
                                size: 11,
                                weight: '500'
                            },
                            color: '#64748b',
                            padding: 8,
                            maxRotation: 45,
                            minRotation: 35,
                            autoSkip: true,
                            autoSkipPadding: 15
                        },
                        title: {
                            display: true,
                            text: 'PERIODE (BULAN)',
                            font: {
                                family: "'Inter', sans-serif",
                                size: 10,
                                weight: '600'
                            },
                            color: '#94a3b8',
                            padding: {
                                top: 12
                            }
                        }
                    }
                },
                elements: {
                    line: {
                        borderJoin: 'round',
                        borderCap: 'round',
                        fill: true
                    },
                    point: {
                        hoverBorderWidth: 3,
                        hoverBorderColor: '#ffffff',
                        hoverBackgroundColor: '#304ffe'
                    }
                },
                layout: {
                    padding: {
                        top: 25,
                        right: 20,
                        bottom: 15,
                        left: 12
                    }
                },
                hover: {
                    mode: 'nearest',
                    intersect: true,
                    animationDuration: 200
                },
                transitions: {
                    show: {
                        animations: {
                            x: {
                                from: 0
                            },
                            y: {
                                from: 0
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
