@props(['paginator', 'options' => []])

@php
    // Parse current filters from request
    $currentFilters = [];
    if (request('filters')) {
        if (is_string(request('filters'))) {
            $currentFilters = json_decode(request('filters'), true) ?: [];
        } elseif (is_array(request('filters'))) {
            $currentFilters = request('filters');
        }
    }
    // Include date range in current filters display if present
    if (request('date_from')) {
        $currentFilters['date_from'] = request('date_from');
    }
    if (request('date_to')) {
        $currentFilters['date_to'] = request('date_to');
    }
@endphp

{{-- Compact search and filter controls --}}
<div class="mb-3">
    {{-- Active Filters Display --}}
    @if(!empty($currentFilters) || request('search'))
        @php
            $activeFilterCount = count($currentFilters) + (request('search') ? 1 : 0);
        @endphp
        <div class="mb-2">
            <small class="text-dark">
                <i class="ti-filter text-primary"></i> فیلترهای فعال ({{ $activeFilterCount }}):
                @if(request('search'))
                    <span class="badge bg-primary text-white ms-1">جستجو: {{ request('search') }}</span>
                @endif
                @if(isset($currentFilters['type']))
                    <span class="badge bg-info text-white ms-1">نوع: {{ $currentFilters['type'] == 'product' ? 'محصول' : 'مواد اولیه' }}</span>
                @endif
                @if(isset($currentFilters['unit_id']))
                    @php
                        $unit = \App\Models\Unit::find($currentFilters['unit_id']);
                    @endphp
                    @if($unit)
                        <span class="badge bg-info text-white ms-1">واحد: {{ $unit->name }}</span>
                    @endif
                @endif
                @if(isset($currentFilters['commodity_id']))
                    @php
                        $commodity = \App\Models\Commodity::find($currentFilters['commodity_id']);
                    @endphp
                    @if($commodity)
                        <span class="badge bg-info text-white ms-1">کالا: {{ $commodity->title }}</span>
                    @endif
                @endif
                @if(isset($currentFilters['from_unit_id']))
                    @php
                        $fromUnit = \App\Models\Unit::find($currentFilters['from_unit_id']);
                    @endphp
                    @if($fromUnit)
                        <span class="badge bg-info text-white ms-1">واحد مبدا: {{ $fromUnit->name }}</span>
                    @endif
                @endif
                @if(isset($currentFilters['to_unit_id']))
                    @php
                        $toUnit = \App\Models\Unit::find($currentFilters['to_unit_id']);
                    @endphp
                    @if($toUnit)
                        <span class="badge bg-info text-white ms-1">واحد مقصد: {{ $toUnit->name }}</span>
                    @endif
                @endif
                @if(isset($currentFilters['status']))
                    @php
                        if(isset($options['status_options']) && isset($options['status_options'][$currentFilters['status']])) {
                            $statusLabel = $options['status_options'][$currentFilters['status']];
                        } else {
                            // Fallback to default status labels
                            $statusLabels = [
                                'awaiting_approval' => 'در انتظار تایید',
                                'approved' => 'تایید شده',
                                'rejected' => 'رد شده',
                                'expired' => 'منقضی شده',
                                'done' => 'تکمیل شده'
                            ];
                            $statusLabel = $statusLabels[$currentFilters['status']] ?? $currentFilters['status'];
                        }
                    @endphp
                    <span class="badge bg-info text-white ms-1">وضعیت: {{ $statusLabel }}</span>
                @endif
                @if((isset($options['show_date_range']) && $options['show_date_range']) && (request()->routeIs('order.chart') || request()->routeIs('order.factory-status')) && (isset($currentFilters['date_from']) || isset($currentFilters['date_to'])))
                    <span class="badge bg-warning text-dark ms-1">
                        تاریخ: {{ $currentFilters['date_from'] ?? '...' }} تا {{ $currentFilters['date_to'] ?? '...' }}
                    </span>
                @endif
                @if(isset($currentFilters['seller_id']))
                    @php
                        $seller = \App\Models\Seller::find($currentFilters['seller_id']);
                    @endphp
                    @if($seller)
                        <span class="badge bg-info text-white ms-1">فروشنده: {{ $seller->comp_name ?? $seller->name }}</span>
                    @endif
                @endif
                @if(isset($currentFilters['role_id']))
                    @php
                        $role = \App\Models\Role::find($currentFilters['role_id']);
                    @endphp
                    @if($role)
                        <span class="badge bg-info text-white ms-1">نقش: {{ $role->name }}</span>
                    @endif
                @endif
                @if(isset($currentFilters['customer_id']))
                    @php
                        $customer = \App\Models\Customer::find($currentFilters['customer_id']);
                    @endphp
                    @if($customer)
                        <span class="badge bg-info text-white ms-1">مشتری: {{ $customer->name }} {{ $customer->comp_name ? '(' . $customer->comp_name . ')' : '' }}</span>
                    @endif
                @endif
                @if(isset($currentFilters['product_id']))
                    @php
                        $product = \App\Models\Commodity::find($currentFilters['product_id']);
                    @endphp
                    @if($product)
                        <span class="badge bg-info text-white ms-1">محصول: {{ $product->title }}</span>
                    @endif
                @endif
            </small>
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-6">
            {{-- Search Input --}}
            @if(isset($options['searchable_fields']) && !empty($options['searchable_fields']))
            <div class="input-group">
                <span class="input-group-text">
                    <i class="ti-search"></i>
                </span>
                <input type="text" id="search-input" 
                       class="form-control" 
                       placeholder="{{ $options['search_placeholder'] ?? 'جستجو...' }}"
                       value="{{ request('search') }}">
                @if(request('search'))
                    <button class="btn btn-outline-secondary" type="button" id="clear-search">
                        <i class="ti-close"></i>
                    </button>
                @endif
            </div>
            @endif
        </div>
        <div class="col-md-6">
            {{-- Per Page Selection --}}
            @if(isset($options['per_page_options']) && !empty($options['per_page_options']))
            <div class="d-flex gap-2 align-items-center">
                <label for="per-page" class="form-label mb-0">تعداد رکورد در صفحه:</label>
                <select class="form-select form-select-sm" id="per-page" style="width: auto; min-width: 80px;">
                    @foreach($options['per_page_options'] as $option)
                        <option value="{{ $option }}" {{ request('per_page', 10) == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            {{-- Filter Controls --}}
            @if(isset($options['filterable_fields']) && !empty($options['filterable_fields']))
            <div class="d-flex gap-2 flex-wrap align-items-center">
            
            {{-- Date Range (placed on the same row as other filters; only for chart view) --}}
            @if(isset($options['show_date_range']) && $options['show_date_range'] && (request()->routeIs('order.chart') || request()->routeIs('order.factory-status')))
            <input type="text" id="date_from" class="form-control form-control-sm usage" placeholder="از تاریخ" autocomplete="off" style="width: auto; min-width: 140px;" value="{{ request('date_from') }}">
            <input type="text" id="date_to" class="form-control form-control-sm usage" placeholder="تا تاریخ" autocomplete="off" style="width: auto; min-width: 140px;" value="{{ request('date_to') }}">
            @endif
            
            @foreach($options['filterable_fields'] as $field)
                @if($field === 'type')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 120px;" 
                            data-filter="type">
                        <option value="">{{ __('pagination.all_types') }}</option>
                        <option value="product" {{ ($currentFilters['type'] ?? '') == 'product' ? 'selected' : '' }}>
                            {{ __('fields.commodity.types.product') }}
                        </option>
                        <option value="material" {{ ($currentFilters['type'] ?? '') == 'material' ? 'selected' : '' }}>
                            {{ __('fields.commodity.types.material') }}
                        </option>
                    </select>
                @elseif($field === 'unit_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 140px;" 
                            data-filter="unit_id">
                        <option value="">{{ __('pagination.all_units') }}</option>
                        @foreach(\App\Models\Unit::orderBy('name')->get() as $unit)
                            <option value="{{ $unit->id }}" 
                                {{ ($currentFilters['unit_id'] ?? '') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->symbol }})
                            </option>
                        @endforeach
                    </select>
                @elseif($field === 'commodity_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 160px;" 
                            data-filter="commodity_id">
                        <option value="">{{ __('pagination.all_commodities') }}</option>
                        @foreach(\App\Models\Commodity::orderBy('title')->get() as $commodity)
                            <option value="{{ $commodity->id }}" 
                                {{ ($currentFilters['commodity_id'] ?? '') == $commodity->id ? 'selected' : '' }}>
                                {{ $commodity->title }}
                            </option>
                        @endforeach
                    </select>
                @elseif($field === 'from_unit_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 140px;" 
                            data-filter="from_unit_id">
                        <option value="">{{ __('pagination.all_from_units') }}</option>
                        @foreach(\App\Models\Unit::orderBy('name')->get() as $unit)
                            <option value="{{ $unit->id }}" 
                                {{ ($currentFilters['from_unit_id'] ?? '') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->symbol }})
                            </option>
                        @endforeach
                    </select>
                @elseif($field === 'to_unit_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 140px;" 
                            data-filter="to_unit_id">
                        <option value="">{{ __('pagination.all_to_units') }}</option>
                        @foreach(\App\Models\Unit::orderBy('name')->get() as $unit)
                            <option value="{{ $unit->id }}" 
                                {{ ($currentFilters['to_unit_id'] ?? '') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->symbol }})
                            </option>
                        @endforeach
                    </select>
                @elseif($field === 'status')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 140px;" 
                            data-filter="status">
                        <option value="">{{ __('pagination.all_statuses') }}</option>
                        @if(isset($options['status_options']))
                            @foreach($options['status_options'] as $value => $label)
                                <option value="{{ $value }}" {{ ($currentFilters['status'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        @else
                            {{-- Fallback to default status options if not provided --}}
                            <option value="awaiting_approval" {{ ($currentFilters['status'] ?? '') == 'awaiting_approval' ? 'selected' : '' }}>
                                در انتظار تایید
                            </option>
                            <option value="approved" {{ ($currentFilters['status'] ?? '') == 'approved' ? 'selected' : '' }}>
                                تایید شده
                            </option>
                            <option value="rejected" {{ ($currentFilters['status'] ?? '') == 'rejected' ? 'selected' : '' }}>
                                رد شده
                            </option>
                            <option value="expired" {{ ($currentFilters['status'] ?? '') == 'expired' ? 'selected' : '' }}>
                                منقضی شده
                            </option>
                            <option value="done" {{ ($currentFilters['status'] ?? '') == 'done' ? 'selected' : '' }}>
                                تکمیل شده
                            </option>
                        @endif
                    </select>
                @elseif($field === 'seller_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 160px;" 
                            data-filter="seller_id">
                        <option value="">{{ __('pagination.all_sellers') }}</option>
                        @foreach(\App\Models\Seller::orderBy('name')->get() as $seller)
                            <option value="{{ $seller->id }}" 
                                {{ ($currentFilters['seller_id'] ?? '') == $seller->id ? 'selected' : '' }}>
                                {{ $seller->comp_name ?? $seller->name }}
                            </option>
                        @endforeach
                    </select>
                @elseif($field === 'role_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 140px;" 
                            data-filter="role_id">
                        <option value="">{{ __('pagination.all_roles') }}</option>
                        @foreach(\App\Models\Role::orderBy('name')->get() as $role)
                            <option value="{{ $role->id }}" 
                                {{ ($currentFilters['role_id'] ?? '') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                @elseif($field === 'customer_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 160px;" 
                            data-filter="customer_id">
                        <option value="">{{ __('pagination.all_customers') }}</option>
                        @foreach(\App\Models\Customer::orderBy('name')->get() as $customer)
                            <option value="{{ $customer->id }}" 
                                {{ ($currentFilters['customer_id'] ?? '') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} {{ $customer->comp_name ? '(' . $customer->comp_name . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                @elseif($field === 'product_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 160px;" 
                            data-filter="product_id">
                        <option value="">{{ __('pagination.all_products') }}</option>
                        @foreach(\App\Models\Commodity::where('type', 'product')->orderBy('title')->get() as $product)
                            <option value="{{ $product->id }}" 
                                {{ ($currentFilters['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                {{ $product->title }}
                            </option>
                        @endforeach
                    </select>
                @endif
            @endforeach
            
            <button class="btn btn-success btn-sm" id="apply-filters" type="button">
                <i class="ti-check"></i> {{ __('pagination.apply_filters') }}
            </button>
            
            @if(!empty($currentFilters) || request('search'))
                <button class="btn btn-outline-secondary btn-sm" id="clear-filters" type="button">
                    <i class="ti-close"></i> {{ __('pagination.clear_filters') }}
                </button>
            @endif
            </div>
            @endif
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Per page change handler
    const perPageSelect = document.getElementById('per-page');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        });
    }
    
    // Search handler
    const searchInput = document.getElementById('search-input');
    const clearSearchBtn = document.getElementById('clear-search');
    
    if (searchInput) {
        // Auto-search on input change (like DataTables)
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                const url = new URL(window.location);
                if (searchInput.value.trim()) {
                    url.searchParams.set('search', searchInput.value.trim());
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            }, 500); // 500ms delay for better UX
        });
        
        // Enter key handler
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                const url = new URL(window.location);
                if (searchInput.value.trim()) {
                    url.searchParams.set('search', searchInput.value.trim());
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }
        });
    }
    
    // Clear search handler
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            const url = new URL(window.location);
            url.searchParams.delete('search');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    }
    
    // Filter handlers
    const applyFiltersBtn = document.getElementById('apply-filters');
    const clearFiltersBtn = document.getElementById('clear-filters');
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const url = new URL(window.location);
            const filters = {};
            
            // Collect all filter values
            document.querySelectorAll('[data-filter]').forEach(function(element) {
                const filterName = element.getAttribute('data-filter');
                const filterValue = element.value;
                if (filterValue && filterValue.trim()) {
                    filters[filterName] = filterValue.trim();
                }
            });
            
            if (Object.keys(filters).length > 0) {
                url.searchParams.set('filters', JSON.stringify(filters));
            } else {
                url.searchParams.delete('filters');
            }
            // Attach date range from chart view inputs if present
            const dateFromInput = document.getElementById('date_from');
            const dateToInput = document.getElementById('date_to');
            const df = dateFromInput ? dateFromInput.value : '';
            const dt = dateToInput ? dateToInput.value : '';
            if (df) { url.searchParams.set('date_from', df); } else { url.searchParams.delete('date_from'); }
            if (dt) { url.searchParams.set('date_to', dt); } else { url.searchParams.delete('date_to'); }
            
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        });
    }
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            const url = new URL(window.location);
            url.searchParams.delete('filters');
            url.searchParams.delete('search');
            url.searchParams.delete('date_from');
            url.searchParams.delete('date_to');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    }
    
    // Filter group is now properly positioned using Bootstrap grid system
});
</script>