@if($summaryStats['totalOrders'] > 0)
    <div class="row mb-3">
        @if(isset($summaryStats['canDeliverCount']))
            <div class="col-md-2">
                <div class="card bg-primary text-white text-center summary-card">
                    <div class="card-body">
                        <h6>کل سفارشات</h6>
                        <h4>{{ $summaryStats['totalOrders'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white text-center summary-card">
                    <div class="card-body">
                        <h6>قابل تحویل</h6>
                        <h4>{{ $summaryStats['canDeliverCount'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-danger text-white text-center summary-card">
                    <div class="card-body">
                        <h6>غیرقابل تحویل</h6>
                        <h4>{{ $summaryStats['cannotDeliverCount'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-info text-white text-center summary-card">
                    <div class="card-body">
                        <h6>ارزش کل</h6>
                        <h6>{{ number_format($summaryStats['totalValue']) }} ریال</h6>
                    </div>
                </div>
            </div>
            @if(isset($summaryStats['totalAmount']))
                <div class="col-md-2">
                    <div class="card bg-warning text-white text-center summary-card">
                        <div class="card-body">
                            <h6>مقدار سفارش</h6>
                            <h6>{{ number_format($summaryStats['totalAmount']) }}</h6>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Simple stats for customer details -->
            <div class="col-md-3">
                <div class="card bg-primary text-white summary-card">
                    <div class="card-body text-center">
                        <h5>کل سفارشات</h5>
                        <h3>{{ $summaryStats['totalOrders'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white summary-card">
                    <div class="card-body text-center">
                        <h5>قابل تحویل</h5>
                        <h3>{{ $summaryStats['canDeliverCount'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white summary-card">
                    <div class="card-body text-center">
                        <h5>غیرقابل تحویل</h5>
                        <h3>{{ $summaryStats['cannotDeliverCount'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white summary-card">
                    <div class="card-body text-center">
                        <h5>ارزش کل</h5>
                        <h3>{{ number_format($summaryStats['totalValue']) }} ریال</h3>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
