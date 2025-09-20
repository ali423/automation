@extends('layouts.main')
@section('title', 'نمایش کالا')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">مشخصات کالا</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        @include('dashboard.commodity.partials.commodity-details')

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('commodity.edit', $commodity) }}" class="btn btn-primary">ویرایش</a>
                                <form method="post" action="{{ route('commodity.destroy', $commodity) }}" class="d-inline w-50">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('آیا از حذف این کالا مطمئن هستید؟');">حذف کالا</button>
                                </form>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('activity.index', [
                                    'object_id' => $commodity->id,
                                    'object_type' => class_basename($commodity),
                                ]) }}"
                                   class="btn btn-dfprimary px-2 px-md-4 m-md-0">تاریخچه تغییرات</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
@endsection
