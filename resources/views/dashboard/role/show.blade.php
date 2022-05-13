@extends('layouts.main')
@section('title','داشبورد')

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
                                <label for="exampleInputEmail111"> نام نقش (فارسی)</label>
                                <input type="text" name="name" value="{{ $role->name }}" class="form-control"
                                       id="exampleInputEmail111" placeholder="حسابدار" autocomplete="off" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail111"> عنوان نقش (انگلیسی)</label>
                                <input type="text" name="title" value="{{ $role->title }}" class="form-control" readonly
                                       id="exampleInputEmail111" placeholder="accountant">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row col-md-12">
                                @foreach($role->permissions as $permission)
                                    <div class="col-md-3">
                                        <div class="form-group"><input type="checkbox" onclick="return false;"
                                                                       checked
                                                                       name="permissions[]"
                                                                       value="{{$permission->id}}"
                                                                       class=""> {{$permission->name}}  </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="row">
                            <a href="{{route('role.edit',$role)}}" class="btn btn-primary mr-2">ویرایش نقش</a>
                            <form method="post" action="{{route('role.destroy',$role)}}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">حذف نقش</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <!-- These plugins only need for the run this page -->
    <script src="{{asset('js/default-assets/basic-form.js')}}"></script>
@endsection



