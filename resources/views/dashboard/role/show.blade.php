@extends('layouts.main')
@section('title', 'نمایش نقش')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">نقش جدید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.name') }}(فارسی)</label>
                                <input type="text" name="name" value="{{ $role->name }}" class="form-control"
                                    id="exampleInputEmail111" placeholder="حسابدار" autocomplete="off" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> {{ __('fields.title') }}(انگلیسی)</label>
                                <input type="text" name="title" value="{{ $role->title }}" class="form-control" readonly
                                    id="exampleInputEmail111" placeholder="accountant">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail111"> {{ __('fields.permissions') }}</label>
                            <div class="row col-md-12">
                                @foreach ($role->permissions as $permission)
                                    <div class="col-md-3">
                                        <div class="form-group"><input type="checkbox" onclick="return false;" checked
                                                name="permissions[]" value="{{ $permission->id }}" class="">
                                            {{ $permission->name }} </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('role.edit', $role) }}" class="btn btn-primary">ویرایش
                                    نقش</a>
                                <form method="post" action="{{ route('role.destroy', $role) }}" class="d-inline w-50">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('آیا از حذف این نقش مطمئن هستید؟');">حذف نقش</button>
                                </form>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <a href="{{ route('activity.index', [
                                    'object_id' => $role->id,
                                    'object_type' => class_basename($role),
                                ]) }}"
                                    class="btn btn-dfprimary my-2 mb-lg-0">تاریخچه تغییرات</a>
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
