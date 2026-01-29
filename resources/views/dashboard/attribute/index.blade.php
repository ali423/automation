@extends('layouts.main')
@section('title', 'لیست ویژگی‌ها')
@section('page_styles')
    <!-- These plugins only need for the run this page -->
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
                    <h4 class="card-title mb-2">لیست ویژگی‌ها</h4>
                    
                    {{-- Pagination Controls --}}
                    <x-pagination-controls :paginator="$attributes" :options="[
                        'searchable_fields' => ['name', 'description'],
                        'per_page_options' => [5, 10, 25, 50, 100],
                        'search_placeholder' => 'جستجو در نام و توضیحات...'
                    ]" />
                    
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="ti-info-circle"></i> 
                            مرتب‌سازی فقط برای رکوردهای صفحه فعلی اعمال می‌شود
                        </small>
                    </div>
                    <table id="datatable-buttons-attribute" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                        <tr>
                            <th>ردیف</th>
                            <th>{{ __('fields.name') }}</th>
                            <th>{{ __('fields.comment') }}</th>
                            <th>{{ __('fields.creator') }}</th>
                            <th>{{ __('fields.created_at') }}</th>
                            <th>{{ __('fields.details') }}</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        @foreach ($attributes as $attribute)
                            <tr>
                                <td>{{ $attributes->firstItem() + $loop->index }}</td>
                                <td>{{ $attribute->name }}</td>
                                <td>{{ $attribute->description ?? '-' }}</td>
                                <td>{{ $attribute->creator->full_name ?? 'سیستم' }}</td>
                                <td>{{ jdate($attribute->created_at)->format('Y/m/d') }}</td>
                                <td>
                                    <a href="{{ route('attribute.show', $attribute) }}" class=""><i class="ti-more-alt font-24"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    
                    {{-- Pagination Links --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            نمایش {{ $attributes->firstItem() }} تا {{ $attributes->lastItem() }} از {{ $attributes->total() }} رکورد
                        </div>
                        <div>
                            {{ $attributes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="{{ asset('js/default-assets/jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-responsive.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/jszip.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/default-assets/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-active.js') }}"></script>
@endsection
