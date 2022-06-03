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
                        <form method="post" action="{{ route('role.store') }}" class="needs-validation" novalidate="">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name"> {{  __('fields.name') }}(فارسی)</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                                        id="name" pattern="[\u0600-\u0605 ؐ-ؚ\u061Cـ ۖ-\u06DD ۟-ۤ ۧ ۨ ۪-ۭ ً-ٕ ٟ ٖ-ٞ ٰ ، ؍ ٫ ٬ ؛ ؞ ؟ ۔ ٭ ٪ ؉ ؊ ؈ ؎ ؏
                                        ۞ ۩ ؆ ؇ ؋ ٠۰0 ١۱1 ٢۲2 ٣۳3 ٤۴4 ٥۵5 ٦۶6 ٧۷7 ٨۸8 ٩۹9 ءٴ۽ آ أ ٲ ٱ ؤ إ ٳ ئ ا ٵ ٮ ب ٻ پ ڀ
                                        ة-ث ٹ ٺ ټ ٽ ٿ ج ڃ ڄ چ ڿ ڇ ح خ ځ ڂ څ د ذ ڈ-ڐ ۮ ر ز ڑ-ڙ ۯ س ش ښ-ڜ ۺ ص ض ڝ ڞ
                                        ۻ ط ظ ڟ ع غ ڠ ۼ ف ڡ-ڦ ٯ ق ڧ ڨ ك ک-ڴ ػ ؼ ل ڵ-ڸ م۾ ن ں-ڽ ڹ ه ھ ہ-ۃ ۿ ەۀ وۥ ٶ
                                        ۄ-ۇ ٷ ۈ-ۋ ۏ ى يۦ ٸ ی-ێ ې ۑ ؽ-ؿ ؠ ے ۓ \u061D]+" placeholder="حسابدار" autocomplete="off" required="">
                                    <div class="invalid-feedback">
                                        لطفاً نام نقش را وارد کنید.
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="title"> {{  __('fields.title') }}(انگلیسی)</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                        id="title" placeholder="accountant" pattern="[a-zA-Z0-9 . - _]+" required="">
                                    <div class="invalid-feedback">
                                        لطفاً عنوان نقش را وارد کنید.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail111"> {{  __('fields.permissions') }}</label>
                                <div class="row col-md-12">
                                    @foreach ($permissions as $permission)
                                        <div class="col-md-3">
                                            <div class="form-group"><input type="checkbox"
                                                    @if (old('permissions') && in_array($permission->id, old('permissions'))) checked @endif name="permissions[]"
                                                    value="{{ $permission->id }}" class="">
                                                {{ $permission->name }} </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">ثبت نقش</button>
                            <a href="{{ route('role.index') }}" class="btn btn-danger">انصراف</a>
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

