@extends('layouts.main')
@section('title','ثبت درخواست فروش محصول')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ثبت درخواست فروش محصول</h4>

                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('withdrawal-request.store') }}"
                              class="needs-validation forms-sample" enctype="multipart/form-data" novalidate="">
                            @csrf
                            <div class="form-row m-3">
                                <div class="form-group col">
                                    <label for="customer_id">نام مشتری</label>
                                    <select id="customer_id" class="form-control" name="customer_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{$customer->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                       مشتری را انتخاب کنید
                                    </div>
                                </div>
                            </div>
                            <div id="product_formul" class="col-lg-12">
                                <p>اطلاعات فروش محصول</p>
                                @include('dashboard.processes.withdrawal-request.partials.commodity-form', [
                                    'index' => 0,
                                    'commodities' => $commodities,
                                    'showRemoveButton' => false
                                ])

                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن محصول</button>
                            </div>

                            <div class="form-group">
                                <label>الصاق فایل به درخواست</label>
                                <input type="file" name="file" class="file-upload-default"
                                       accept="image/*,.pdf,.zip,.rar">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled=""
                                           placeholder="فایل از نوع تصویر ، pdf یا zip">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary"
                                                type="button">انتخاب فایل</button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group mb-20">
                                <label for="comment">توضیحات</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment"
                                          rows="6">{{old('comment')}}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">ثبت درخواست</button>
                            <a href="{{ route('withdrawal-request.index') }}" class="btn btn-danger">انصراف</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script type="text/javascript">
        // Preload commodity units data
        var commodityUnitsData = {};
        @foreach($commodities as $commodity)
            commodityUnitsData[{{ $commodity->id }}] = [
                @foreach($commodity->selectable_units as $unit)
                    {
                        id: {{ $unit->id }},
                        name: '{{ $unit->name }}',
                        symbol: '{{ $unit->symbol }}',
                        display_name: '{{ $unit->name }} ({{ $unit->symbol }})'
                    }@if(!$loop->last),@endif
                @endforeach
            ];
        @endforeach

        // Preload commodity types data
        var commodityTypesData = {};
        @foreach($commodities as $commodity)
            commodityTypesData[{{ $commodity->id }}] = '{{ $commodity->type }}';
        @endforeach

        // add row
        $("#addRow").click(function() {
            var currentIndex = $('.inputFormRow').length;
            var html = '<div class="inputFormRow form-row shadow p-4 mb-3" style="position: relative;"> <i class="removeRow ti-close" type="button" style="position: absolute; top: 10px; left: 10px; cursor: pointer; font-size: 1.5rem; color: #dc3545; z-index: 10;"></i> <div class="form-group col-md-6"> <label> {{ __('fields.commodity.name') }}</label> <select class="form-control commodity-select" name="commodity_id[' + currentIndex + ']" onchange="pricefunc(this)" required> <option value="">انتخاب کنید</option>@foreach ($commodities as $commodity)<option value="{{ $commodity->id }}">{{ $commodity->title }}</option>@endforeach</select> <div class="invalid-feedback">محصول را انتخاب کنید.</div> </div> <div class="form-group col-md-6"> <label> {{ __('fields.unit') }}</label> <select class="form-control unit-select" name="unit_id[' + currentIndex + ']" required> <option value="">انتخاب کنید...</option></select> <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div> </div> <div class="form-group col-md-4"> <label> {{  __('fields.commodity.amount') }}</label> <input type="number" class="form-control amount-input" min="1" name="amount[' + currentIndex + ']" autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}" pattern="[0-9 .]" required=""> <div class="invalid-feedback">لطفاً {{  __('fields.commodity.amount') }} را وارد کنید. </div></div><div class="priceholder form-group col-md-4"><label> {{  __('fields.sell-price_per_unit') }}</label><input type="number" class="form-control price-input" min="0" step="any" name="price[' + currentIndex + ']" autocomplete="off" placeholder="{{  __('fields.sell-price_per_unit') }}" pattern="[0-9 .]" ><div class="invalid-feedback">{{ __('fields.sell-price_per_unit') }} را وارد کنید.</div></div></div>';
            $('#newRow').append(html);
        });

        // remove row
        $(document).on('click', '.removeRow', function() {
            $(this).closest('.inputFormRow').remove();
            // Re-index all remaining rows
            $('.inputFormRow').each(function(index) {
                $(this).find('.commodity-select').attr('name', 'commodity_id[' + index + ']');
                $(this).find('.unit-select').attr('name', 'unit_id[' + index + ']');
                $(this).find('.amount-input').attr('name', 'amount[' + index + ']');
                $(this).find('.price-input').attr('name', 'price[' + index + ']');
            });
        });

        function pricefunc(el) {
            var container = el.closest('.inputFormRow');
            var unitSelect = container.querySelector('.unit-select');
            var priceInput = container.querySelector('.price-input');
            var id = el.value;

            // Loading states
            unitSelect.disabled = true;
            unitSelect.innerHTML = '<option value="">در حال بارگذاری...</option>';
            priceInput.disabled = true;
            priceInput.placeholder = 'در حال بارگذاری...';

            if (!id) {
                // reset
                unitSelect.disabled = false;
                unitSelect.innerHTML = '<option value="">انتخاب کنید...</option>';
                priceInput.value = '';
                priceInput.placeholder = '';
                priceInput.disabled = false;
                return;
            }

            // Get commodity type from preloaded data (kept for potential future logic)
            var type = commodityTypesData[id];

            // Populate selectable units from preloaded data
            var units = commodityUnitsData[id];
            unitSelect.innerHTML = '<option value="">انتخاب کنید...</option>';
            if (units) {
                units.forEach(function(unit) {
                    var option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = unit.display_name;
                    unitSelect.appendChild(option);
                });
            }
            unitSelect.disabled = false;

            // Fetch price via AJAX
            $.ajax({
                url: '/inventory-ajax/' + id,
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    var price = (response && response.data) ? response.data.price : null;
                    priceInput.value = (price ?? '');
                    priceInput.placeholder = '';
                    priceInput.disabled = false;
                },
                error: function() {
                    priceInput.value = '';
                    priceInput.placeholder = 'نامشخص';
                    priceInput.disabled = false;
                }
            });
        }
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/active.js') }}"></script>
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{asset('js/default-assets/file-upload.js')}}"></script>
@endsection

