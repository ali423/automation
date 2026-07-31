@extends('layouts.main')
@section('title', 'قیمت مواد اولیه')
@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/default-assets/datatables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/buttons.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/default-assets/select.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables-td.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">قیمت مواد اولیه</h4>

                    @include('dashboard.commodity.partials.price-tabs', ['activeTab' => 'material-prices'])

                    @isset($commodities)
                        <x-pagination-controls :paginator="$commodities" :options="$options" />
                    @endisset

                    @if(!empty($canEditPrices))
                        <form method="POST" action="{{ route('commodity.material-prices.update', request()->query()) }}" id="bulk-material-prices-form">
                            @csrf
                            @method('PUT')
                            <div class="d-flex justify-content-end mb-3">
                                <button type="submit" class="btn btn-primary">
                                    ذخیره تغییرات
                                </button>
                            </div>
                    @endif

                    <div class="table-responsive">
                        <table id="datatable-buttons-material-prices" class="table table-striped dt-responsive nowrap w-100">
                            <thead class="text-center">
                                <tr>
                                    <th>ردیف</th>
                                    <th>{{ __('fields.title') }}</th>
                                    <th>{{ __('fields.commodity.number') }}</th>
                                    <th>{{ __('fields.purchase_price') }} (ریال)</th>
                                    <th>{{ __('fields.unit') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @isset($commodities)
                                    @forelse($commodities as $index => $c)
                                        <tr data-id="{{ $c->id }}">
                                            <td>{{ $commodities->firstItem() + $index }}</td>
                                            <td>{{ $c->title }}</td>
                                            <td>{{ $c->number }}</td>
                                            <td>
                                                @if(!empty($canEditPrices))
                                                    <input type="hidden" name="prices[{{ $index }}][id]" value="{{ $c->id }}">
                                                    <input type="number"
                                                           step="0.01"
                                                           min="100"
                                                           name="prices[{{ $index }}][purchase_price]"
                                                           class="form-control form-control-sm text-center purchase-price-input"
                                                           data-original="{{ old('prices.'.$index.'.purchase_price', $c->purchase_price !== null ? $c->purchase_price : '') }}"
                                                           value="{{ old('prices.'.$index.'.purchase_price', $c->purchase_price !== null ? $c->purchase_price : '') }}"
                                                           required>
                                                @else
                                                    {{ $c->purchase_price !== null ? number_format($c->purchase_price) : '-' }}
                                                @endif
                                            </td>
                                            <td>{{ $c->unit ? $c->unit->name . ' (' . $c->unit->symbol . ')' : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                {{ __('pagination.no_results') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                @endisset
                            </tbody>
                        </table>
                    </div>

                    @if(!empty($canEditPrices))
                        </form>
                    @endif
                </div>
                @isset($commodities)
                    <div class="card-footer">
                        <x-pagination-navigation :paginator="$commodities" />
                    </div>
                @endisset
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="{{ asset('js/default-assets/jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-responsive.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.sorting.persian.js') }}"></script>
    <script src="{{ asset('js/commodity-bulk-price-save.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            bindDirtyOnlyPriceForm('#bulk-material-prices-form', 'material');

            $('#datatable-buttons-material-prices').DataTable({
                dom: 't',
                paging: false,
                searching: false,
                ordering: true,
                order: [],
                info: false,
                language: {
                    paginate: { previous: 'قبلی', next: 'بعدی' }
                }
            });
        });
    </script>
@endsection
