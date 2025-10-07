@extends('layouts.main')
@section('title', 'داشبورد')

@section('page_styles')
<!-- These plugins only need for the run this page -->
<link rel="stylesheet" href="{{ asset('js/default-assets/vector-map/jquery-jvectormap-2.0.2.css') }}">
<style>
    .main-dashboard-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    .main-dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .main-tab-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .main-tab-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .main-dashboard-card:hover .main-tab-header::before {
        left: 100%;
    }

    .main-tab-icon {
        font-size: 2.5rem;
        margin-bottom: 10px;
        display: block;
    }

    .main-tab-title {
        font-size: 1.4rem;
        font-weight: 600;
        margin: 0;
    }

    .sub-tabs-container {
        padding: 0;
        background: #f8f9fa;
    }

    .sub-tab-item {
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #495057;
    }

    .sub-tab-item:last-child {
        border-bottom: none;
    }

    .sub-tab-item:hover {
        background: #e3f2fd;
        color: #1976d2;
        text-decoration: none;
        transform: translateX(5px);
    }

    .sub-tab-icon {
        font-size: 1.2rem;
        margin-left: 15px;
        width: 20px;
        text-align: center;
    }

    .sub-tab-text {
        flex: 1;
        font-size: 1rem;
        font-weight: 500;
    }

    .sub-tab-arrow {
        font-size: 0.9rem;
        opacity: 0.6;
        transition: all 0.2s ease;
    }

    .sub-tab-item:hover .sub-tab-arrow {
        opacity: 1;
        transform: translateX(3px);
    }

    .status-tab {
        background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
    }

    .status-tab .sub-tab-item:hover {
        background: rgba(76, 175, 80, 0.1);
        color: #2e7d32;
    }

    .production-tab {
        background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
    }

    .purchase-tab {
        background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
    }

    .order-tab {
        background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%);
    }

    .inventory-tab {
        background: linear-gradient(135deg, #00bcd4 0%, #0097a7 100%);
    }

    .factory-status-tab {
        background: linear-gradient(135deg, #795548 0%, #5d4037 100%);
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 20px;
        margin-bottom: 60px;
        /* Add space before footer */
    }

    .dashboard-container {
        min-height: calc(100vh - 200px);
        /* Ensure minimum height */
        padding-bottom: 40px;
        /* Extra padding at bottom */
    }

    /* Ensure proper spacing between content and footer */
    .main-content {
        margin-bottom: 60px;
    }

    /* Footer styling */
    .footer-area {
        background: #f8f9fa !important;
        border-top: 1px solid #e9ecef !important;
        padding: 20px 0 !important;
        margin-top: 40px !important;
        min-height: 60px !important;
    }

    .footer-area p {
        margin: 0 !important;
        color: #6c757d !important;
        font-size: 14px !important;
    }

    .footer-area a {
        color: #007bff !important;
        text-decoration: none !important;
    }

    /* Raw Materials Chart Styles */
    #dashboard-raw-materials-charts {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        margin-bottom: 20px;
    }

    .mini-dashboard-chart {
        flex: 1 1 300px;
        min-width: 240px;
        max-width: 350px;
        height: 260px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        padding: 12px;
        margin-bottom: 0;
        box-sizing: border-box;
    }

    @media (max-width: 900px) {
        .mini-dashboard-chart {
            flex-basis: 48%;
            min-width: 180px;
            max-width: 100%;
        }
    }

    @media (max-width: 600px) {
        .mini-dashboard-chart {
            flex-basis: 100%;
            min-width: 120px;
            max-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .main-tab-header {
            padding: 15px;
        }

        .main-tab-icon {
            font-size: 2rem;
        }

        .main-tab-title {
            font-size: 1.2rem;
        }
    }
</style>
@endsection

@section('content')

<!-- Modern Dashboard -->
<div class="container-fluid dashboard-container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-center mb-4" style="color: #2c3e50; font-weight: 700;">داشبورد سیستم اتوماسیون</h2>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- تولید (Production) -->
        @if(Gate::check('read_production') || Gate::check('create_production'))
        <div class="main-dashboard-card">
            <div class="main-tab-header production-tab">
                <i class="ti-settings main-tab-icon"></i>
                <h3 class="main-tab-title">تولید</h3>
            </div>
            <div class="sub-tabs-container">
                @can('create_production',App\Models\ProductionRequest::class)
                <a href="{{ route('production-request.create') }}" class="sub-tab-item">
                    <i class="ti-write sub-tab-icon"></i>
                    <span class="sub-tab-text">اظهار تولید</span>
                    <i class="ti-angle-left sub-tab-arrow"></i>
                </a>
                @endcan
                @can('read_production',App\Models\ProductionRequest::class)
                <a href="{{ route('production-request.index') }}" class="sub-tab-item">
                    <i class="ti-list-ol sub-tab-icon"></i>
                    <span class="sub-tab-text">لیست اظهار تولید</span>
                    <i class="ti-angle-left sub-tab-arrow"></i>
                </a>
                @endcan
            </div>
        </div>
        @endif

        <!-- خرید کالا (Purchase) -->
        @if(Gate::check('read_importing') || Gate::check('create_importing'))
        <div class="main-dashboard-card">
            <div class="main-tab-header purchase-tab">
                <i class="ti-truck main-tab-icon"></i>
                <h3 class="main-tab-title">خرید کالا</h3>
            </div>
            <div class="sub-tabs-container">
                @can('create_importing',App\Models\ImportingRequest::class)
                <a href="{{ route('importing-request.create') }}" class="sub-tab-item">
                    <i class="ti-write sub-tab-icon"></i>
                    <span class="sub-tab-text">ثبت ورود جدید</span>
                    <i class="ti-angle-left sub-tab-arrow"></i>
                </a>
                @endcan
                @can('read_importing',App\Models\ImportingRequest::class)
                <a href="{{ route('importing-request.index') }}" class="sub-tab-item">
                    <i class="ti-list-ol sub-tab-icon"></i>
                    <span class="sub-tab-text">لیست ورود کالا ها</span>
                    <i class="ti-angle-left sub-tab-arrow"></i>
                </a>
                @endcan
            </div>
        </div>
        @endif

        <!-- سفارشات (Orders) -->
        @if(Gate::check('read_order') || Gate::check('create_order'))
        <div class="main-dashboard-card">
            <div class="main-tab-header order-tab">
                <i class="ti-receipt main-tab-icon"></i>
                <h3 class="main-tab-title">سفارشات</h3>
            </div>
            <div class="sub-tabs-container">
                @can('create_order',App\Models\Order::class)
                <a href="{{ route('order.create') }}" class="sub-tab-item">
                    <i class="ti-write sub-tab-icon"></i>
                    <span class="sub-tab-text">ثبت سفارش جدید</span>
                    <i class="ti-angle-left sub-tab-arrow"></i>
                </a>
                @endcan
                @can('read_order',App\Models\Order::class)
                <a href="{{ route('order.index') }}" class="sub-tab-item">
                    <i class="ti-list-ol sub-tab-icon"></i>
                    <span class="sub-tab-text">لیست سفارشات</span>
                    <i class="ti-angle-left sub-tab-arrow"></i>
                </a>
                @endcan
            </div>
        </div>
        @endif

        <!-- وضعیت موجودی کالای کارخانه (Inventory Status) -->
        @if(Gate::check('read_inventory') || Gate::check('read_commodity'))
        <div class="main-dashboard-card">
            <div class="main-tab-header inventory-tab">
                <i class="ti-package main-tab-icon"></i>
                <h3 class="main-tab-title">وضعیت موجودی کالای کارخانه</h3>
            </div>
            <div class="sub-tabs-container">
                @can('read_inventory',App\Models\Inventory::class)
                <a href="{{ route('inventory.index') }}" class="sub-tab-item">
                    <i class="ti-eye sub-tab-icon"></i>
                    <span class="sub-tab-text">مشاهده موجودی</span>
                    <i class="ti-angle-left sub-tab-arrow"></i>
                </a>
                @endcan
            </div>
        </div>
        @endif

        <!-- وضعیت سفارشات (Factory Status) -->
        @if(Gate::check('read_order'))
        <div class="main-dashboard-card">
            <div class="main-tab-header factory-status-tab">
                <i class="ti-bar-chart main-tab-icon"></i>
                <h3 class="main-tab-title">وضعیت سفارشات</h3>
            </div>
            <div class="sub-tabs-container">
                <a href="{{ route('order.factory-status') }}" class="sub-tab-item">
                    <i class="ti-factory sub-tab-icon"></i>
                    <span class="sub-tab-text">مشاهده وضعیت کارخانه</span>
                    <i class="ti-angle-left sub-tab-arrow"></i>
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Raw Materials Chart Section -->
    @if(Gate::check('read_order') || Gate::check('read_inventory'))
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-2">وضعیت مواد اولیه</h4>
                    <p class="text-muted small mb-3">نمودار بر اساس فیلترهای انتخاب شده در لیست سفارشات به‌روزرسانی می‌شود. فقط سفارشات با وضعیت "در حال پردازش" در محاسبات نمودار لحاظ می‌شوند.</p>
                    <div id="dashboard-raw-materials-charts"></div>
                    <div class="mt-3" id="dashboard-raw-materials-table-wrapper">
                        <div class="table-responsive">
                            <table id="dashboard-raw-materials-table" class="table table-sm table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>مواد اولیه</th>
                                        <th>نیاز</th>
                                        <th>موجودی</th>
                                        <th>اختلاف</th>
                                        <th>واحد</th>
                                        <th>وضعیت</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@section('page_scripts')
<!-- These plugins only need for the run this page -->
<script src="{{ asset('js/default-assets/apexchart.min.js') }}"></script>
<script src="{{ asset('js/default-assets/peity.min.js') }}"></script>
<script src="{{ asset('js/default-assets/peity-demo.js') }}"></script>
<script src="{{ asset('js/canvas.min.js') }}"></script>

<script>
    $(document).ready(function() {
        // Load and render raw materials chart
        loadRawMaterialsChart();
        
        function loadRawMaterialsChart() {
            $.ajax({
                url: '{{ route("dashboard.chart.data") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    renderRawMaterialsCharts({
                        names: response.names,
                        amounts: response.amounts,
                        units: response.units,
                        inventory: response.inventory
                    });
                    renderRawMaterialsTable({
                        names: response.names,
                        amounts: response.amounts,
                        units: response.units,
                        inventory: response.inventory
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching chart data:', xhr);
                    $('#dashboard-raw-materials-charts').html('<div class="alert alert-warning text-center">خطا در دریافت اطلاعات نمودار</div>');
                }
            });
        }

        function renderRawMaterialsCharts(data) {
            $('#dashboard-raw-materials-charts').empty();
            
            if (data.names.length === 0) {
                $('#dashboard-raw-materials-charts').append('<div class="alert alert-info text-center">هیچ سفارش در حال پردازشی انتخاب نشده است یا محصولات انتخاب شده فرمول مواد اولیه ندارند.</div>');
                return;
            }
            
            data.names.forEach(function(name, idx) {
                var chartId = 'dashboard-raw-materials-chart-' + idx;
                $('#dashboard-raw-materials-charts').append('<div id="'+chartId+'" class="mini-dashboard-chart"></div>');
                
                
                var chartData = {
                    chart: { 
                        height: 220, 
                        type: "bar",
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: { 
                        bar: { 
                            horizontal: false, 
                            columnWidth: "55%", 
                            endingShape: "rounded" 
                        } 
                    },
                    dataLabels: { enabled: false },
                    stroke: { 
                        show: true, 
                        width: 2, 
                        colors: ["transparent"] 
                    },
                    series: [
                        { 
                            name: "مواد اولیه مورد نیاز", 
                            data: [data.amounts[idx]] 
                        },
                        { 
                            name: "موجودی مواد اولیه", 
                            data: [data.inventory[idx]] 
                        }
                    ],
                    colors: ["#e74c3c", "#27ae60"],
                    xaxis: { 
                        categories: [name],
                        labels: {
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: { 
                        title: { 
                            text: data.units[idx],
                            style: {
                                fontSize: '12px'
                            }
                        },
                        labels: {
                            style: {
                                fontSize: '11px'
                            }
                        }
                    },
                    fill: { opacity: 1 },
                    tooltip: {
                        y: {
                            formatter: function (e) {
                                return e + ' ' + data.units[idx];
                            },
                        },
                    },
                    legend: {
                        position: 'top',
                        fontSize: '12px'
                    }
                };
                
                var chart = new ApexCharts(document.getElementById(chartId), chartData);
                chart.render();
            });
        }

        function renderRawMaterialsTable(data) {
            var $table = $('#dashboard-raw-materials-table');
            var $tbody = $table.find('tbody');

            $tbody.empty();

            if (!data.names || data.names.length === 0) {
                $tbody.append('<tr><td colspan="6" class="text-center text-muted">داده‌ای برای نمایش وجود ندارد</td></tr>');
                return;
            }

            data.names.forEach(function(name, idx) {
                var need = (data.amounts && data.amounts[idx] !== undefined) ? parseFloat(data.amounts[idx]) : 0;
                var inv = (data.inventory && data.inventory[idx] !== undefined) ? parseFloat(data.inventory[idx]) : 0;
                var diff = inv - need;
                var unit = (data.units && data.units[idx]) ? data.units[idx] : '';
                var statusOk = parseFloat(inv) >= parseFloat(need);
                var statusBadge = statusOk
                    ? '<span class="badge bg-success">کافی</span>'
                    : '<span class="badge bg-danger">کمبود</span>';

                $tbody.append(
                    '<tr>' +
                        '<td>' + name + '</td>' +
                        '<td data-order="' + need + '">' + need.toLocaleString() + ' ' + unit + '</td>' +
                        '<td data-order="' + inv + '">' + inv.toLocaleString() + ' ' + unit + '</td>' +
                        '<td data-order="' + diff + '">' + diff.toLocaleString() + ' ' + unit + '</td>' +
                        '<td>' + unit + '</td>' +
                        '<td>' + statusBadge + '</td>' +
                    '</tr>'
                );
            });
        }

        // Add smooth animations and interactions
        $('.main-dashboard-card').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
            $(this).addClass('fade-in-up');
        });


        // Add click ripple effect
        $('.sub-tab-item').on('click', function(e) {
            var $this = $(this);
            var ripple = $('<span class="ripple"></span>');
            var rect = this.getBoundingClientRect();
            var size = Math.max(rect.width, rect.height);
            var x = e.clientX - rect.left - size / 2;
            var y = e.clientY - rect.top - size / 2;

            ripple.css({
                width: size,
                height: size,
                left: x,
                top: y
            });

            $this.append(ripple);

            setTimeout(function() {
                ripple.remove();
            }, 600);
        });

        // Add hover effects for better UX
        $('.main-dashboard-card').hover(
            function() {
                $(this).find('.main-tab-header').addClass('pulse');
            },
            function() {
                $(this).find('.main-tab-header').removeClass('pulse');
            }
        );
    });

    // Add CSS for animations
    $('<style>')
        .prop('type', 'text/css')
        .html(`
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .pulse {
            animation: pulse 0.3s ease-in-out;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    `)
        .appendTo('head');
</script>

@endsection