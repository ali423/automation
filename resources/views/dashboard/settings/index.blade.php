@extends('layouts.main')
@section('title', 'تنظیمات سیستم')

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
                    <h4 class="card-title mb-2">تنظیمات سیستم</h4>
                    
                    <div class="alert alert-info mb-3">
                        <i class="ti-info-circle"></i>
                        <strong>نکته:</strong> تنظیمات سیستم قابل حذف نیستند زیرا برای عملکرد صحیح سیستم ضروری هستند. 
                        برای غیرفعال کردن تنظیمات از دکمه‌های فعال/غیرفعال استفاده کنید.
                    </div>
                    
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('settings.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> افزودن تنظیمات جدید
                        </a>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <table id="datatable-buttons-settings" class="table table-striped dt-responsive nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th>ردیف</th>
                                <th>نام</th>
                                <th>کلید</th>
                                <th>مقدار</th>
                                <th>نوع</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @php($i = 1)
                            @forelse($settings as $setting)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $setting->name }}</td>
                                    <td><code>{{ $setting->key }}</code></td>
                                        <td>
                                            @if($setting->type === 'boolean')
                                                <span class="badge badge-{{ $setting->value ? 'success' : 'danger' }}">
                                                    {{ $setting->value ? 'فعال' : 'غیرفعال' }}
                                                </span>
                                            @elseif($setting->type === 'json')
                                                <pre class="mb-0">{{ json_encode(json_decode($setting->value), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @else
                                                {{ $setting->value }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $setting->type }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('settings.toggle', $setting) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-{{ $setting->is_active ? 'warning' : 'success' }}">
                                                    {{ $setting->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('settings.show', $setting) }}"><i class="ti-more-alt font-24"></i></a>
                                        </td>
                                    </tr>
                                    @php($i++)
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        هیچ تنظیماتی یافت نشد.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/jquery.datatables.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-responsive.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/datatable-button.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.html5.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.flash.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.print.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/button.colVis.min.js') }}"></script>
    <script src="{{ asset('js/default-assets/dataTables.select.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#datatable-buttons-settings').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Persian.json"
                },
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "pageLength": 25,
                "order": [[1, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [6] }
                ]
            }).buttons().container().appendTo('#datatable-buttons-settings_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
