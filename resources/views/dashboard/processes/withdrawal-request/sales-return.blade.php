@extends('layouts.main')
@section('title', 'برگشت از فروش')

@section('page_styles')
    <style>
        .return-item {
            background-color: white;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        .return-type-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .return-type-badge.normal {
            background-color: #cfe2ff;
            color: #084298;
        }
        .return-type-badge.add_back {
            background-color: #d4edda;
            color: #155724;
        }
        .return-type-badge.deduct {
            background-color: #f8d7da;
            color: #721c24;
        }
        .return-type-badge.qty_adjustment {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">برگشت از فروش - درخواست شماره {{ $request->number }}</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">

                        {{-- Original Order Reference Section --}}
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <strong>📋 سفارش اصلی (مرجع)</strong>
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>کالا</th>
                                            <th>مقدار</th>
                                            <th>واحد</th>
                                            <th>قیمت واحد</th>
                                            <th>جمع</th>
                                            <th>وضعیت</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($request->commodities as $commodity)
                                            @php
                                                $totalReturned = $request->adjustments
                                                    ->where('commodity_id', $commodity->id)
                                                    ->where('adjustment_type', 'sales_return')
                                                    ->sum('amount');
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $commodity->title }}</strong></td>
                                                <td>{{ $commodity->pivot->amount }}</td>
                                                <td>{{ $commodity->pivot->unit->name ?? '-' }}</td>
                                                <td>{{ $commodity->pivot->price ? number_format($commodity->pivot->price, 0) : '-' }}</td>
                                                <td>{{ $commodity->pivot->price ? number_format($commodity->pivot->price * $commodity->pivot->amount, 0) : '-' }}</td>
                                                <td>
                                                    @if($totalReturned > 0)
                                                        <span class="badge badge-warning">برگشت شده: {{ $totalReturned }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">هیچ کالایی ثبت نشده</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Adjustments History Section --}}
                        @php
                            $relevantAdjustments = $request->adjustments->filter(function($adj) {
                                return $adj->adjustment_type === 'sales_return' || 
                                       (str_contains($adj->reason ?? '', '[تصحیح ارسال:'));
                            });
                        @endphp
                        
                        @if($relevantAdjustments->count() > 0)
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <strong>📊 تاریخچه تصحیحات و برگشت‌ها</strong>
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>نوع</th>
                                            <th>کالا</th>
                                            <th>مقدار</th>
                                            <th>تاریخ</th>
                                            <th>توضیحات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($relevantAdjustments as $adjustment)
                                            <tr>
                                                <td>
                                                    @if($adjustment->adjustment_type === 'sales_return')
                                                        <span class="badge badge-info">برگشت</span>
                                                    @elseif(str_contains($adjustment->reason ?? '', '[تصحیح ارسال: برگشت]'))
                                                        <span class="badge badge-success">برگشت تصحیح</span>
                                                    @elseif(str_contains($adjustment->reason ?? '', '[تصحیح ارسال: کسر]'))
                                                        <span class="badge badge-danger">کسر تصحیح</span>
                                                    @elseif(str_contains($adjustment->reason ?? '', '[تصحیح ارسال: مقدار]'))
                                                        <span class="badge badge-warning">تصحیح مقدار</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $adjustment->adjustment_type }}</span>
                                                    @endif
                                                </td>
                                                <td><strong>{{ $adjustment->commodity->title ?? '-' }}</strong></td>
                                                <td>{{ abs($adjustment->amount) }}</td>
                                                <td>{{ $adjustment->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <small>{{ Str::limit($adjustment->reason ?? '-', 50) }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr class="my-4">
                        @endif

                        <form method="post" action="{{ route('sales-return.withdrawal.submit', $request->id) }}" class="needs-validation" novalidate>
                            @csrf
                            
                            {{-- Unified Returns Section --}}
                            <div class="form-section">
                                <h5 class="mb-3">
                                    <strong>برگشت‌های فروش و تصحیحات ارسالی</strong>
                                </h5>
                                
                                <div class="alert alert-info">
                                    <strong>راهنما:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li><strong>برگشت عادی:</strong> مشتری محصول را برگردانده</li>
                                        <li><strong>ثبت‌شده اما ارسال نشده:</strong> محصول ثبت شده بود ولی واقعاً ارسال نشد</li>
                                        <li><strong>ارسال شده اما ثبت نشده:</strong> محصول ارسال شد ولی در سیستم ثبت نشده بود</li>
                                        <li><strong>تصحیح مقدار:</strong> محصول با مقدار اشتباه ارسال شد (افزایش/کاهش)</li>
                                    </ul>
                                </div>

                                <div id="returns-container"></div>

                                <button type="button" class="btn btn-sm btn-primary" onclick="addReturnRow(event)">
                                    <i class="fa fa-plus"></i> افزودن برگشت/تصحیح
                                </button>
                            </div>

                            <hr class="my-4">

                            {{-- General Notes --}}
                            <div class="form-section">
                                <h5 class="mb-3">
                                    <strong>یادداشت‌های عمومی</strong>
                                </h5>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label>توضیحات (اختیاری)</label>
                                        <textarea name="reason" class="form-control" rows="3" placeholder="هرگونه توضیح اضافی درباره برگشت یا تصحیح">{{ old('reason') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-info">
                                    <i class="fa fa-check"></i> ثبت برگشت و تصحیح موجودی
                                </button>
                                <a href="{{ route('withdrawal-request.show', $request) }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> بازگشت
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
    <script>
        // Data passed from controller
        const allCommodities = @json($allCommodities);
        const allUnits = @json($allUnits);
        const commodityUnitsData = @json($commodityUnitsData);
        // Include both original commodities and those added via corrections
        const requestCommodityIds = @json($requestCommodityIds ?? $request->commodities->pluck('id')->values());
        const addedCommodityIds = @json($addedCommodityIds ?? []);

        let returnRowCounter = 0;

        // Return types with descriptions
        const returnTypes = {
            'normal': {
                label: 'برگشت عادی',
                description: 'مشتری محصول را برگردانده',
                badge: 'normal'
            },
            'add_back': {
                label: 'ثبت‌شده اما ارسال نشده',
                description: 'محصول ثبت شده بود ولی واقعاً ارسال نشد',
                badge: 'add_back'
            },
            'deduct': {
                label: 'ارسال شده اما ثبت نشده',
                description: 'محصول ارسال شد ولی در سیستم ثبت نشده بود',
                badge: 'deduct'
            },
            'qty_adjustment': {
                label: 'تصحیح مقدار',
                description: 'محصول با مقدار اشتباه ارسال شد',
                badge: 'qty_adjustment'
            }
        };

        function getAllowedCommoditiesByType(type) {
            if (type === 'deduct') {
                return allCommodities.filter(c => c.type === 'product');
            }
            if (['normal', 'add_back', 'qty_adjustment'].includes(type)) {
                return allCommodities.filter(c => requestCommodityIds.includes(c.id));
            }
            return allCommodities;
        }

        function addReturnRow(event) {
            if (event) event.preventDefault();
            
            const container = document.getElementById('returns-container');
            const rowIndex = returnRowCounter++;

            const unitsOptions = allUnits
                .map(unit => `<option value="${unit.id}">${unit.name}</option>`)
                .join('');

            const commodityOptions = allCommodities
                .map(c => `<option value="${c.id}">${c.title}</option>`)
                .join('');

            const typeOptions = Object.entries(returnTypes)
                .map(([key, value]) => `<option value="${key}">${value.label}</option>`)
                .join('');

            const html = `
                <div class="return-item" data-index="${rowIndex}">
                    <button type="button" class="btn btn-sm btn-outline-danger float-left" onclick="removeReturnRow(this)">
                        <i class="fa fa-trash"></i>
                    </button>
                    
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label>نوع برگشت</label>
                            <select name="return_type[]" class="form-control form-control-sm type-select" onchange="updateTypeDescription(this); updateCommodityOptionsByType(this); toggleDirectionField(this);">
                                <option value="">انتخاب کنید...</option>
                                ${typeOptions}
                            </select>
                            <small class="form-text text-muted type-description mt-1"></small>
                        </div>

                        <div class="form-group col-md-3">
                            <label>کالا</label>
                            <select name="return_commodity_id[]" class="form-control form-control-sm commodity-select" onchange="updateCommodityUnits(this)">
                                <option value="">انتخاب کنید...</option>
                                ${commodityOptions}
                            </select>
                        </div>
                        
                        <div class="form-group col-md-2">
                            <label>مقدار</label>
                            <input type="number" name="return_amount[]" class="form-control form-control-sm" 
                                   step="0.01" min="0" placeholder="0" required>
                        </div>
                        
                        <div class="form-group col-md-2 direction-wrapper d-none">
                            <label>جهت تصحیح</label>
                            <select name="return_direction[]" class="form-control form-control-sm direction-select">
                                <option value="decrease">کاهش</option>
                                <option value="increase">افزایش</option>
                            </select>
                        </div>
                        
                        <div class="form-group col-md-2">
                            <label>واحد</label>
                            <select name="return_unit_id[]" class="form-control form-control-sm unit-select">
                                ${unitsOptions}
                            </select>
                        </div>
                        
                        <div class="form-group col-md-2">
                            <label>دلیل</label>
                            <input type="text" name="return_reason[]" class="form-control form-control-sm" 
                                   placeholder="مثلاً: معیوب">
                        </div>
                    </div>
                </div>
            `;

            // Create a temporary div to hold the new row
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            // Append the first child (the return-item div) to the container
            container.appendChild(tempDiv.firstElementChild);
        }

        function updateTypeDescription(select) {
            const type = select.value;
            const description = select.closest('.return-item').querySelector('.type-description');
            
            if (type && returnTypes[type]) {
                description.textContent = returnTypes[type].description;
            } else {
                description.textContent = '';
            }
        }

        function updateCommodityOptionsByType(typeSelect) {
            const row = typeSelect.closest('.return-item');
            const commoditySelect = row.querySelector('.commodity-select');
            const unitSelect = row.querySelector('.unit-select');
            const selectedType = typeSelect.value;
            const previousCommodity = commoditySelect.value;
            const allowedCommodities = getAllowedCommoditiesByType(selectedType);

            const commodityOptions = allowedCommodities
                .map(c => `<option value="${c.id}">${c.title}</option>`)
                .join('');

            commoditySelect.innerHTML = `<option value="">انتخاب کنید...</option>${commodityOptions}`;

            const stillAllowed = allowedCommodities.some(c => String(c.id) === String(previousCommodity));
            if (stillAllowed) {
                commoditySelect.value = previousCommodity;
            } else {
                commoditySelect.value = '';
                const allUnitsOptions = allUnits
                    .map(unit => `<option value="${unit.id}">${unit.name}</option>`)
                    .join('');
                unitSelect.innerHTML = allUnitsOptions;
            }
        }

        function toggleDirectionField(typeSelect) {
            const row = typeSelect.closest('.return-item');
            const directionWrapper = row.querySelector('.direction-wrapper');
            const directionSelect = row.querySelector('.direction-select');
            if (typeSelect.value === 'qty_adjustment') {
                directionWrapper.classList.remove('d-none');
                directionSelect.disabled = false;
            } else {
                directionWrapper.classList.add('d-none');
                directionSelect.disabled = true;
                directionSelect.value = 'decrease';
            }
        }

        function updateCommodityUnits(select) {
            const commodityId = select.value;
            const unitSelect = select.closest('.return-item').querySelector('.unit-select');
            
            if (!commodityId || !commodityUnitsData[commodityId]) {
                const allUnitsOptions = allUnits
                    .map(unit => `<option value="${unit.id}">${unit.name}</option>`)
                    .join('');
                unitSelect.innerHTML = allUnitsOptions;
                return;
            }

            const units = commodityUnitsData[commodityId];
            const options = Object.values(units)
                .map(unit => `<option value="${unit.id}">${unit.name} (${unit.symbol})</option>`)
                .join('');
            
            unitSelect.innerHTML = options;
        }

        function removeReturnRow(button) {
            button.closest('.return-item').remove();
        }

        // Initialize with one empty row
        document.addEventListener('DOMContentLoaded', function() {
            addReturnRow();
        });
    </script>
@endsection

