@extends('layouts.main')
@section('title','ثبت درخواست فروش کالا')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ثبت درخواست فروش کالا</h4>

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
                                <p>اطلاعات فروش کالا</p>
                                <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                    <div class="form-group col-md-6">
                                        <label for="commodity_id"> {{ __('fields.commodity.name') }}</label>
                                        <select id="commodity_id" class="form-control" name="commodity_id[0]" onchange="pricefunc(this)" required>
                                            <option value="">انتخاب کنید</option>
                                            @foreach ($commodities as $commodity)
                                                <option value="{{ $commodity->id }}">{{ $commodity->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="unit"> {{ __('fields.unit') }}</label>
                                        <select id="unit" class="form-control" name="unit_id[0]" required>
                                            <option value="">انتخاب کنید...</option>
                                            <!-- Options will be filled by preloaded data -->
                                        </select>
                                        <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="amount"> {{  __('fields.commodity.amount') }}</label>
                                        <input type="number" id="amount" min="1" name="amount[0]" class="form-control"
                                               autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}" pattern="[0-9 .]"  required="">
                                        <div class="invalid-feedback">
                                            لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="pieces_per_box">تعداد در کارتن</label>
                                        <input type="number" id="pieces_per_box" min="1" name="pieces_per_box[0]" class="form-control"
                                               autocomplete="off" placeholder="مثال: 24" pattern="[0-9]" required="">
                                        <div class="invalid-feedback">
                                            لطفاً تعداد در کارتن را وارد کنید.
                                        </div>
                                    </div>
                                    <div id="priceholder" class="form-group col-md-4">
                                        <label for="price"> {{  __('fields.sell-price_per_unit') }}</label>
                                        <input type="number" id="price" min="1" name="price[0]" class="form-control"
                                               autocomplete="off" placeholder="{{  __('fields.sell-price_per_unit') }}" pattern="[0-9 .]" >
                                    </div>
                                </div>

                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن کالا</button>
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
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"> <div class="form-group col-md-6"> <label for="commodity_id"> {{ __('fields.commodity.name') }}</label> <select id="commodity_id" class="form-control" name="commodity_id[]"  onchange="pricefunc(this)" required> <option value="">انتخاب کنید</option>@foreach ($commodities as $commodity)<option value="{{ $commodity->id }}">{{ $commodity->title }}</option>@endforeach</select> <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div> </div> <div class="form-group col-md-6"> <label for="unit"> {{ __('fields.unit') }}</label> <select id="unit" class="form-control" name="unit_id[]" required> <option value="">انتخاب کنید...</option></select> <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div> </div> <div class="form-group col-md-4"> <label for="amount"> {{  __('fields.commodity.amount') }}</label> <input type="number" min="1" name="amount[]" class="form-control"id="amount" autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}" pattern="[0-9 .]"  required=""> <div class="invalid-feedback">لطفاً {{  __('fields.commodity.amount') }} را وارد کنید. </div></div><div class="form-group col-md-4"><label for="pieces_per_box">تعداد در کارتن</label><input type="number" min="1" name="pieces_per_box[]" class="form-control" autocomplete="off" placeholder="مثال: 24" pattern="[0-9]" required=""><div class="invalid-feedback">لطفاً تعداد در کارتن را وارد کنید.</div></div><div id="priceholder" class="form-group col-md-4"><label for="price"> {{  __('fields.sell-price_per_unit') }}</label><input type="number" id="price" min="1" name="price[]" class="form-control" autocomplete="off" placeholder="{{  __('fields.sell-price_per_unit') }}" pattern="[0-9 .]" ><div class="invalid-feedback">{{ __('fields.sell-price_per_unit') }} را وارد کنید.</div></div> <i id="removeRow" type="submit" class="ti-close"></i></div></div>';
            $('#newRow').append(html);
            document.querySelectorAll('#inputFormRow').forEach((element,index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id['+index+']');
                element.querySelector('#unit').setAttribute('name', 'unit_id['+index+']');
                element.querySelector('#amount').setAttribute('name', 'amount['+index+']');
                element.querySelector('#pieces_per_box').setAttribute('name', 'pieces_per_box['+index+']');
                element.querySelector('#price').setAttribute('name', 'price['+index+']');
            });
        });

        // remove row
        $(document).on('click', '#removeRow', function() {
            $(this).closest('#inputFormRow').remove();
            document.querySelectorAll('#inputFormRow').forEach((element,index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id['+index+']');
                element.querySelector('#unit').setAttribute('name', 'unit_id['+index+']');
                element.querySelector('#amount').setAttribute('name', 'amount['+index+']');
                element.querySelector('#pieces_per_box').setAttribute('name', 'pieces_per_box['+index+']');
                element.querySelector('#price').setAttribute('name', 'price['+index+']');
            });
        });

        function pricefunc(el) {
            var unitSelect = el.closest('#inputFormRow').querySelector('#unit');
            var id = el.value;

            // Clear unit options first
            unitSelect.innerHTML = '<option value="">انتخاب کنید...</option>';

            if (!id) {
                return;
            }

            // Get commodity type from preloaded data
            var type = commodityTypesData[id];
            if (type === "material") {
                // For withdrawal requests, we might want to show price field for all commodities
                // or handle it differently based on business logic
            }

            // Get selectable units from preloaded data
            var units = commodityUnitsData[id];
            if (units) {
                units.forEach(function(unit) {
                    var option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = unit.display_name;
                    unitSelect.appendChild(option);
                });
            }
        }
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/active.js') }}"></script>
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{asset('js/default-assets/file-upload.js')}}"></script>
@endsection

