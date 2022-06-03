@extends('layouts.main')
@section('title','داشبورد')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">ویرایش کاربر</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('user.update', $user) }}" class="needs-validation"
                            novalidate="">
                            @method('PATCH')
                            @csrf
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail111"> {{  __('fields.name') }}</label>
                                    <input type="text" name="name" value="{{ $user->name }}" class="form-control"
                                           id="exampleInputEmail111" placeholder="{{  __('fields.name')  }}"
                                           autocomplete="off"
                                    >
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail111"> {{  __('fields.lastname') }}</label>
                                    <input type="text" name="lastname" value="{{ $user->lastname }}"
                                           class="form-control"
                                           id="exampleInputEmail111" placeholder="{{  __('fields.lastname') }}"
                                           autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail111"> {{  __('fields.user_name') }}</label>
                                    <input type="text" name="user_name" value="{{ $user->user_name}}"
                                           class="form-control"
                                           id="exampleInputEmail111" placeholder="{{  __('fields.user_name') }}"
                                           autocomplete="off" readonly
                                    >
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputState"> {{  __('fields.role.name') }}</label>
                                    <select id="inputState" class="form-control" name="role">
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}"
                                                    @if($user->role_id == $role->id)
                                                    selected
                                                @endif
                                            >{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail111"> {{  __('fields.status') }}</label>
                                    <select id="inputState" class="form-control" name="status">
                                        <option  value="active"
                                                 @if($user->status == 'active')
                                                 selected
                                            @endif
                                        >فعال</option>
                                        <option value="inactive"
                                                @if($user->status != 'active')
                                                selected
                                            @endif
                                        >غیر فعال</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">ویرایش</button>
                            <a href="{{ route('user.index') }}" class="btn btn-danger">انصراف</a>
                        </form>
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

