{{-- Shared tabs for commodity list / product prices / material prices --}}
@php($activeTab = $activeTab ?? 'index')
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'index' ? 'active' : '' }}"
           @if($activeTab === 'index') aria-current="page" href="#" @else href="{{ route('commodity.index', request()->query()) }}" @endif>
            لیست کالاها
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'product-prices' ? 'active' : '' }}"
           @if($activeTab === 'product-prices') aria-current="page" href="#" @else href="{{ route('commodity.prices', request()->query()) }}" @endif>
            قیمت محصولات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'material-prices' ? 'active' : '' }}"
           @if($activeTab === 'material-prices') aria-current="page" href="#" @else href="{{ route('commodity.material-prices', request()->query()) }}" @endif>
            قیمت مواد اولیه
        </a>
    </li>
</ul>
