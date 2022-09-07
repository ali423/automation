@extends('layouts.main')
@section('title','ویرایش درخواست ورود کالا به انبار')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش درخواست ورود کالا به انبار</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('importing-request.update', $request) }}"
                              class="needs-validation"
                              novalidate="">
                            @method('PATCH')
                            @csrf
                            <div class="form-row m-3">
                                <div class="form-group col">
                                    <label for="seller_id">نام فروشنده</label>
                                    <select id="seller_id" class="form-control" name="seller_id" required>
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}"
                                                    @if($request->seller_id == $seller->id)
                                                    selected
                                                @endif
                                            >{{$seller->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        فروشنده را انتخاب کنید
                                    </div>
                                </div>
                            </div>
                            <div id="product_formul" class="col-lg-12">
                                <p>اطلاعات ورود کالا به انبار</p>
                                @foreach($request->commodities as $request_commodity)
                                    <div id="inputFormRow" class="form-row shadow p-4 mb-3">
                                        <div class="form-group col-md-6">
                                            <label for="commodity_id"> {{ __('fields.commodity.name') }}</label>
                                            <select id="commodity_id" class="form-control" name="commodity_id[]"
                                                    onchange="pricefunc(this)" required>
                                                <option value="">انتخاب کنید</option>
                                                @foreach ($commodities as $commodity)
                                                    <option value="{{ $commodity->id }}"
                                                            @if($request_commodity->id ==$commodity->id )
                                                            selected
                                                        @endif
                                                    >{{ $commodity->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <img class="d-none" src onerror='priceload(this)'>
                                            <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب
                                                کنید.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="unit"> {{ __('fields.unit') }}</label>
                                            <select id="unit" class="form-control" name="unit[]" required>
                                                <option value="">انتخاب کنید...</option>
                                                @foreach( __('fields.commodity.units') as $key=>$value)
                                                    <option value="{{$key}}"
                                                            @if($key == $request_commodity->pivot->unit)
                                                            selected
                                                        @endif
                                                    >{{$value}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="warehouse_id"> {{ __('fields.warehouse.name') }}</label>
                                            <select id="warehouse_id" class="form-control" name="warehouse_id[]"
                                                    required>
                                                <option value="">انتخاب کنید</option>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}"
                                                            @if($warehouse->id == $request_commodity->pivot->warehouses_id)
                                                            selected
                                                        @endif
                                                    >{{ $warehouse->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">{{ __('fields.warehouse.name') }} را انتخاب
                                                کنید.
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="amount"> {{  __('fields.commodity.amount') }}</label>
                                            <input type="number" id="amount" min="1" name="amount[]"
                                                   value="{{$request_commodity->pivot->amount}}" class="form-control"
                                                   autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}"
                                                   pattern="[0-9 .]" required="">
                                            <div class="invalid-feedback">
                                                لطفاً {{  __('fields.commodity.amount') }} را وارد کنید.
                                            </div>
                                        </div>
                                        <div id="priceholder" class="form-group col-md-3 d-none">
                                            <label for="price"> {{  __('fields.purchase_unit_price') }}</label>
                                            <input type="number" id="price" min="1" name="purchase_price[]"
                                                   class="form-control"
                                                   autocomplete="off"
                                                   placeholder=" {{  __('fields.purchase_unit_price') }}"
                                                   value="{{$request_commodity->pivot->purchase_price}}"
                                                   pattern="[0-9 .]">
                                        </div>
                                        <i id="removeRow" type="submit" class="ti-close"></i>
                                    </div>

                                @endforeach
                                <div id="newRow"></div>
                                <button id="addRow" type="button" class="btn btn-dfprimary mb-3">+ افزودن</button>
                            </div>

                            @foreach($request->comments as $comment)
                                <div class="form-group mb-20">
                                    <label for="comment"> {{$comment->user->full_name}} در تاریخ
                                        : {{  \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($comment->created_at)) }}</label>
                                    <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment"
                                              rows="6" disabled>{{$comment->body}}</textarea>
                                </div>
                            @endforeach
                            <div class="form-group">
                                <label>الصاق فایل به درخواست</label>
                                <input type="file" name="file" class="file-upload-default"
                                       accept="image/*,.pdf,.zip,.rar">
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled=""
                                           placeholder="فایل از نوع تصویر ، pdf یا zip">
                                    <span class="input-group-append">
                                                    <button class="file-upload-browse btn btn-primary" type="button">انتخاب فایل</button>
                                                </span>
                                </div>
                            </div>
                            <div class="form-group mb-20">
                                <label for="comment">{{ __('fields.comment')}}</label>
                                <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment"
                                          rows="6">{{old('comment')}}</textarea>
                            </div>
                            <div class="col-xl-12 height-card box-margin">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="bg-transparent d-flex align-items-center justify-content-between">
                                            <div class="widgets-card-title">
                                                <h5 class="card-title">فایل ضمیمه شده</h5>
                                            </div>
                                        </div>
                                    @foreach($request->files as $file)
                                        <!-- Single Download File -->
                                            <div
                                                class="widget-download-file d-flex align-items-center justify-content-between mb-4">
                                                <div class="d-flex align-items-center mr-3">
                                                    <div class="download-file-icon mr-3">
                                                        <img src="{{asset('img/filemanager-img/1.png')}}" alt="">
                                                    </div>
                                                    <div class="user-text-table">
                                                        <h6 class="d-inline-block font-15 mb-0">{{$file->name}}</h6>
                                                        <p class="mb-0"> {{$file->user->full_name}} در تاریخ
                                                            : {{  \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($file->created_at)) }}</p>
                                                    </div>
                                                </div>
                                                <a href="{{asset(str_replace('public', 'storage', $file->source))}}"
                                                   download="proposed_file_name"
                                                   class="download-link badge badge-primary badge-pill p-2 font-16"><i
                                                        class="ti-download"></i></a>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">ویرایش</button>
                            <a href="{{ route('importing-request.index') }}" class="btn btn-danger">انصراف</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script type="text/javascript">
        // add row
        $("#addRow").click(function () {
            var html = '<div id="inputFormRow" class="form-row shadow p-4 mb-3"> <div class="form-group col-md-6"> <label for="commodity_id"> {{ __('fields.commodity.name') }}</label> <select id="commodity_id" class="form-control" name="commodity_id[]"  onchange="pricefunc(this)" required> <option value="">انتخاب کنید</option>@foreach ($commodities as $commodity)<option value="{{ $commodity->id }}">{{ $commodity->title }}</option>@endforeach</select><img class="d-none" src onerror="priceload(this)"> <div class="invalid-feedback">{{ __('fields.commodity.name') }} را انتخاب کنید.</div> </div> <div class="form-group col-md-6"> <label for="unit"> {{ __('fields.unit') }}</label> <select id="unit" class="form-control" name="unit[]" required> <option value="">انتخاب کنید...</option>@foreach( __('fields.commodity.units') as $key=>$value)<option value="{{$key}}">{{$value}}</option>@endforeach</select> <div class="invalid-feedback">{{ __('fields.unit') }} را انتخاب کنید</div> </div> <div class="form-group col-md-6"> <label for="warehouse_id"> {{ __('fields.warehouse.name') }}</label> <select id="warehouse_id" class="form-control" name="warehouse_id[]" required> <option value="">انتخاب کنید</option>@foreach ($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->title }}</option>@endforeach</select> <div class="invalid-feedback">{{ __('fields.warehouse.name') }} را انتخاب کنید.</div> </div> <div class="form-group col-md-3"> <label for="amount"> {{  __('fields.commodity.amount') }}</label> <input type="number" min="1" name="amount[]" class="form-control"id="amount" autocomplete="off" placeholder="{{  __('fields.commodity.amount') }}" pattern="[0-9 .]"  required=""> <div class="invalid-feedback">لطفاً {{  __('fields.commodity.amount') }} را وارد کنید. </div></div> <div id="priceholder" class="form-group col-md-3 d-none"><label for="price"> قیمت خرید</label><input type="number" id="price" min="1" name="purchase_price[]" class="form-control"autocomplete="off" placeholder="قیمت خرید" pattern="[0-9 .]"  ><div class="invalid-feedback">لطفاً قیمت خرید را وارد کنید.</div></div> <i id="removeRow" type="submit" class="ti-close"></i></div></div>';
            $('#newRow').append(html);
            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id[' + index + ']');
                element.querySelector('#unit').setAttribute('name', 'unit[' + index + ']');
                element.querySelector('#warehouse_id').setAttribute('name', 'warehouse_id[' + index + ']');
                element.querySelector('#amount').setAttribute('name', 'amount[' + index + ']');
                element.querySelector('#price').setAttribute('name', 'purchase_price[' + index + ']');
            });
        });

        // remove row
        $(document).on('click', '#removeRow', function () {
            $(this).closest('#inputFormRow').remove();
            document.querySelectorAll('#inputFormRow').forEach((element, index) => {
                element.querySelector('#commodity_id').setAttribute('name', 'commodity_id[' + index + ']');
                element.querySelector('#unit').setAttribute('name', 'unit[' + index + ']');
                element.querySelector('#warehouse_id').setAttribute('name', 'warehouse_id[' + index + ']');
                element.querySelector('#amount').setAttribute('name', 'amount[' + index + ']');
                element.querySelector('#price').setAttribute('name', 'purchase_price[' + index + ']');
            });
        });

        function pricefunc(el) {
            var holder = el.closest('#inputFormRow').querySelector('#priceholder');

            //ajax ...
            var id = el.value; //request
            var res = 'material'; //resposnse

            if (id !== "") {
                if (res === "material") {
                    holder.classList.remove('d-none');
                } else {
                    if (!(holder.classList.contains('d-none'))) {
                        holder.classList.add('d-none');
                    }
                }
            } else {
                if (!(holder.classList.contains('d-none'))) {
                    holder.classList.add('d-none');
                }
            }
        }

        function priceload(el) {
            var holder = el.closest('#inputFormRow').querySelector('#priceholder');
            var select = el.closest('#inputFormRow').querySelector('#commodity_id');
            var id = select.value; //request

            //ajax ...
            $.ajax({
                url: '/commodity-type-ajax/' + id,
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    var type = response['type'];
                    if (id !== "") {
                        if (type === "material") {
                            holder.classList.remove('d-none');
                        } else {
                            if (!(holder.classList.contains('d-none'))) {
                                holder.classList.add('d-none');
                            }
                        }
                    }
                }
            });


        }
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/active.js') }}"></script>

    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script src="{{asset('js/default-assets/file-upload.js')}}"></script>

@endsection

