{{-- File Attachments Partial for Withdrawal Requests --}}
@if(isset($files) && $files->count() > 0)
    <div class="col-xl-12 height-card box-margin">
        <div class="card">
            <div class="card-body">
                <div class="bg-transparent d-flex align-items-center justify-content-between">
                    <div class="widgets-card-title">
                        <h5 class="card-title">فایل ضمیمه شده</h5>
                    </div>
                </div>
                @foreach ($files as $file)
                    <!-- Single Download File -->
                    <div class="widget-download-file d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center mr-3">
                            <div class="download-file-icon mr-3">
                                <img src="{{ asset('img/filemanager-img/1.png') }}" alt="">
                            </div>
                            <div class="user-text-table">
                                <h6 class="d-inline-block font-15 mb-0">{{ $file->name }}</h6>
                                <p class="mb-0"> {{ $file->user ? $file->user->full_name : 'کاربر نامشخص' }} در تاریخ :
                                    {{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($file->created_at)) }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ asset(str_replace('public', 'storage', $file->source)) }}"
                           download="proposed_file_name"
                           class="download-link badge badge-primary badge-pill p-2 font-16"><i
                                class="ti-download"></i></a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
