@extends('layouts.main')
@section('title', 'لیست گزارش خرید کالا')
@section('page_styles')
    <!-- These plugins only need for the run this page -->
    <link rel="stylesheet" href="{{ asset('css/default-assets/datatables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/select.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables-td.css') }}">
    
    <style>
        .price-display-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #e9ecef;
        }
        
        .price-display {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .price-display .text-primary {
            font-weight: bold;
            font-size: 1.1em;
        }
        
        .price-display .text-success {
            font-weight: bold;
            font-size: 1.1em;
        }
        
        .conversion-status {
            background: #e7f3ff;
            padding: 6px 10px;
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }
        
        #unit-selector {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 0.9em;
            height: auto;
            min-height: 32px;
            max-width: 180px;
            transition: border-color 0.3s ease;
        }
        
        #unit-selector:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.1rem rgba(0, 123, 255, 0.25);
            outline: none;
        }
        
        #unit-selector:disabled {
            background-color: #f8f9fa;
            opacity: 0.6;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .d-flex.align-items-center {
            gap: 8px;
        }
        
        .loading-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12 col-xl-12">
            <div class="card box-margin">
                <div class="card-body">
                    <div class="float-right"><i class="fa fa-codiepie text-warning font-60"></i></div>
                    <span class="badge badge-warning"> کالای تحت گذارش : {{ $requests['title'] }}</span>
                    
                    <div class="price-display-section mt-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-2">میانگین قیمت خرید</h4>
                                <div class="d-flex align-items-center flex-wrap">
                                    <span class="me-2">هر</span>
                                    <div class="form-group me-2 mb-2">
                                        <select id="unit-selector" class="form-control" aria-label="انتخاب واحد">
                                            @foreach($availableUnits as $unit)
                                                <option value="{{ $unit->id }}" 
                                                        data-symbol="{{ $unit->symbol }}"
                                                        @if($unit->id == $commodity->unit_id) selected @endif>
                                                    {{ $unit->name }} ({{ $unit->symbol }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span class="me-2">{{ $requests['title'] }}</span>
                                    <span class="price-display">
                                        <span id="price-value" class="h5 text-primary">{{ number_format($requests['avr_price']) }}</span>
                                        <span class="text-muted">ریال</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="conversion-status" id="conversion-status" style="display: none;">
                                    <small class="text-muted">
                                        <i class="fa fa-info-circle"></i>
                                        <span id="conversion-info"></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <p class="mb-0 mt-2">
                            <span class="text-muted">بازه زمانی:</span>
                            <span class="fw-bold">{{ $requests['date_from'] }} الی {{ $requests['date_to'] }}</span>
                        </p>
                    </div>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-2">لیست گزارش خرید کالا</h4>
                    <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>ردیف</th>
                                <th> {{ __('fields.commodity.name') }}</th>
                                <th> {{ __('fields.commodity.amount') }}</th>
                                <th> {{ __('fields.purchase_unit_price') }}</th>
                                <th> {{ __('fields.unit') }}</th>
                                <th>{{ __('fields.created_at') }}</th>
                                <th>{{ __('fields.details') }}</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            @php($i = 1)
                            @foreach ($requests['requests'] as $request)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{$requests['title']  }}</td>
                                    <td>{{$request['amount']  }}</td>
                                    <td>{{$request['product_purchase_price']  }}</td>
                                    <td>{{ $request['unit'] }}</td>
                                    <td>{{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d', strtotime($request['created_at'] )) }}
                                    </td>
                                    <td><a href="{{ route('importing-request.show', $request['request_id'] ) }}" class=""><i class="ti-more-alt font-24"></i></a>
                                    </td>
                                </tr>
                                @php($i++)
                            @endforeach
                        </tbody>
                    </table>

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
@endsection

@section('page_scripts')

    <script>
        $(document).ready(function() {
            const basePrice = {{ $requests['avr_price'] }};
            const commodityId = {{ $commodity->id }};
            const baseUnitId = {{ $commodity->unit_id }};
            let currentUnitId = baseUnitId;
            
            // Initialize with base price
            updatePriceDisplay(basePrice, false);
            
            $("#unit-selector").change(function() {
                const selectedUnitId = parseInt($(this).val());
                
                if (selectedUnitId === baseUnitId) {
                    // Show base price if same unit
                    updatePriceDisplay(basePrice, false);
                    hideConversionStatus();
                } else {
                    // Convert price using API
                    convertPrice(basePrice, baseUnitId, selectedUnitId, commodityId);
                }
            });
            
            function convertPrice(price, fromUnitId, toUnitId, commodityId) {
                // Show loading state
                showLoadingState();
                
                $.ajax({
                    url: '{{ route("importing.report.convert-price") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        commodity_id: commodityId,
                        from_unit_id: fromUnitId,
                        to_unit_id: toUnitId,
                        price: price
                    },
                    success: function(response) {
                        if (response.success) {
                            updatePriceDisplay(response.converted_price, true);
                            showConversionStatus(fromUnitId, toUnitId, response.converted_price);
                        } else {
                            showError('خطا در تبدیل واحد: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'خطا در ارتباط با سرور';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showError(errorMessage);
                    },
                    complete: function() {
                        hideLoadingState();
                    }
                });
            }
            
            function updatePriceDisplay(price, isConverted) {
                const formattedPrice = new Intl.NumberFormat('fa-IR').format(Math.round(price));
                $('#price-value').text(formattedPrice);
                
                if (isConverted) {
                    $('#price-value').addClass('text-success').removeClass('text-primary');
                } else {
                    $('#price-value').addClass('text-primary').removeClass('text-success');
                }
            }
            
            function showConversionStatus(fromUnitId, toUnitId, convertedPrice) {
                const fromUnitName = getUnitName(fromUnitId);
                const toUnitName = getUnitName(toUnitId);
                const conversionInfo = `تبدیل از ${fromUnitName} به ${toUnitName}`;
                
                $('#conversion-info').text(conversionInfo);
                $('#conversion-status').show();
            }
            
            function hideConversionStatus() {
                $('#conversion-status').hide();
            }
            
            function getUnitName(unitId) {
                return $('#unit-selector option[value="' + unitId + '"]').text().split(' (')[0];
            }
            
            function showLoadingState() {
                $('#price-value').html('<i class="fa fa-spinner fa-spin"></i>');
                $('#unit-selector').prop('disabled', true);
            }
            
            function hideLoadingState() {
                $('#unit-selector').prop('disabled', false);
            }
            
            function showError(message) {
                $('#price-value').text('خطا').addClass('text-danger');
                alert(message);
            }
        });
    </script>

    <script src="{{ asset('js/default-assets/jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-responsive.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/jszip.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/default-assets/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.print.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.sorting.persian.js') }}"></script>
    <script src="{{ asset('js/default-assets/customDataTable.js') }}"></script>

@endsection
