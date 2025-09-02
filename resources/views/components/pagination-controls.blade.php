@props(['paginator', 'options'])

{{-- DataTables-style pagination controls following consistent module patterns --}}
<div class="dataTables_wrapper">
    <div class="row">
        <div class="col-sm-12 col-md-6">
            {{-- DataTables length (per page selection) --}}
            <div class="dataTables_length">
                <label for="per-page">
                    {{ __('pagination.per_page') }}:
                    <select id="per-page" class="form-select form-select-sm">
                        @foreach([10, 25, 50, 100] as $perPage)
                            <option value="{{ $perPage }}" {{ $paginator->perPage() == $perPage ? 'selected' : '' }}>
                                {{ $perPage }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            {{-- DataTables filter (search) --}}
            @if(isset($options['searchable_fields']) && !empty($options['searchable_fields']))
            <div class="dataTables_filter">
                <label>
                    جستجو:
                    <input type="text" id="search-input" 
                           placeholder="{{ __('pagination.search_placeholder') }}"
                           value="{{ request('search') }}">
                </label>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Advanced Filters Row - Following consistent module patterns --}}
@if(isset($options['filterable_fields']) && !empty($options['filterable_fields']))
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            @foreach($options['filterable_fields'] as $field)
                @if($field === 'status')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 140px;" 
                            data-filter="status">
                        <option value="">{{ __('pagination.all_statuses') }}</option>
                        <option value="pending" {{ request('filters.status') == 'pending' ? 'selected' : '' }}>
                            {{ __('fields.order.status.pending') }}
                        </option>
                        <option value="done" {{ request('filters.status') == 'done' ? 'selected' : '' }}>
                            {{ __('fields.order.status.done') }}
                        </option>
                    </select>
                @elseif($field === 'type')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 140px;" 
                            data-filter="type">
                        <option value="">{{ __('pagination.all_types') }}</option>
                        <option value="product" {{ request('filters.type') == 'product' ? 'selected' : '' }}>
                            {{ __('fields.commodity.types.product') }}
                        </option>
                        <option value="material" {{ request('filters.type') == 'material' ? 'selected' : '' }}>
                            {{ __('fields.commodity.types.material') }}
                        </option>
                    </select>
                @elseif($field === 'customer_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 160px;" 
                            data-filter="customer_id">
                        <option value="">{{ __('pagination.all_customers') }}</option>
                        @foreach(\App\Models\Customer::orderBy('name')->get() as $customer)
                            <option value="{{ $customer->id }}" 
                                {{ request('filters.customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                @elseif($field === 'unit_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 140px;" 
                            data-filter="unit_id">
                        <option value="">{{ __('pagination.all_units') }}</option>
                        @foreach(\App\Models\Unit::orderBy('name')->get() as $unit)
                            <option value="{{ $unit->id }}" 
                                {{ request('filters.unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->symbol }})
                            </option>
                        @endforeach
                    </select>
                @elseif($field === 'role_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 140px;" 
                            data-filter="role_id">
                        <option value="">{{ __('pagination.all_roles') }}</option>
                        @foreach(\App\Models\Role::orderBy('name')->get() as $role)
                            <option value="{{ $role->id }}" 
                                {{ request('filters.role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                @elseif($field === 'seller_id')
                    <select class="form-select form-select-sm" style="width: auto; min-width: 160px;" 
                            data-filter="seller_id">
                        <option value="">{{ __('pagination.all_sellers') }}</option>
                        @foreach(\App\Models\Seller::orderBy('full_name')->get() as $seller)
                            <option value="{{ $seller->id }}" 
                                {{ request('filters.seller_id') == $seller->id ? 'selected' : '' }}>
                                {{ $seller->full_name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    {{-- Generic filter input for other fields --}}
                    <input type="text" class="form-control form-control-sm" 
                           style="width: 150px;" 
                           data-filter="{{ $field }}"
                           placeholder="{{ ucfirst($field) }}"
                           value="{{ request("filters.{$field}") }}">
                @endif
            @endforeach
            
            <button class="btn btn-success btn-sm" id="apply-filters" type="button">
                {{ __('pagination.apply_filters') }}
            </button>
            
            <button class="btn btn-outline-secondary btn-sm" id="clear-filters" type="button">
                {{ __('pagination.clear_filters') }}
            </button>
        </div>
    </div>
</div>
@endif

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
            
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        });
    }
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            const url = new URL(window.location);
            url.searchParams.delete('filters');
            url.searchParams.delete('search');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });
    }
    
    // Filter group is now properly positioned using Bootstrap grid system
});
</script>
