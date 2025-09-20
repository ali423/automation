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
                       placeholder="جستجو در عنوان، شماره یا شناسه کالا..."
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
            {{-- Filter Controls --}}
            @if(isset($options['filterable_fields']) && !empty($options['filterable_fields']))
            <div class="d-flex gap-2 flex-wrap align-items-center">
            
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