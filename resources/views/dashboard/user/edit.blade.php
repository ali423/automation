@extends('layouts.main')
@section('title', 'داشبورد')

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
                                    <label for="exampleInputEmail111"> {{ __('fields.name') }}</label>
                                    <input type="text" name="name" value="{{ $user->name }}" class="form-control"
                                           id="exampleInputEmail111" placeholder="{{ __('fields.name') }}"
                                           autocomplete="off" pattern="[\u0600-\u0605 ؐ-ؚ\u061Cـ ۖ-\u06DD ۟-ۤ ۧ ۨ ۪-ۭ ً-ٕ ٟ ٖ-ٞ ٰ ، ؍ ٫ ٬ ؛ ؞ ؟ ۔ ٭ ٪ ؉ ؊ ؈ ؎ ؏
                                        ۞ ۩ ؆ ؇ ؋ ٠۰0 ١۱1 ٢۲2 ٣۳3 ٤۴4 ٥۵5 ٦۶6 ٧۷7 ٨۸8 ٩۹9 ءٴ۽ آ أ ٲ ٱ ؤ إ ٳ ئ ا ٵ ٮ ب ٻ پ ڀ
                                        ة-ث ٹ ٺ ټ ٽ ٿ ج ڃ ڄ چ ڿ ڇ ح خ ځ ڂ څ د ذ ڈ-ڐ ۮ ر ز ڑ-ڙ ۯ س ش ښ-ڜ ۺ ص ض ڝ ڞ
                                        ۻ ط ظ ڟ ع غ ڠ ۼ ف ڡ-ڦ ٯ ق ڧ ڨ ك ک-ڴ ػ ؼ ل ڵ-ڸ م۾ ن ں-ڽ ڹ ه ھ ہ-ۃ ۿ ەۀ وۥ ٶ
                                        ۄ-ۇ ٷ ۈ-ۋ ۏ ى يۦ ٸ ی-ێ ې ۑ ؽ-ؿ ؠ ے ۓ \u061D]+" required>
                                    <div class="invalid-feedback">این فیلد نباید خالی باشد. (از حروف فارسی استفاده کنید)
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail111"> {{ __('fields.lastname') }}</label>
                                    <input type="text" name="lastname" value="{{ $user->lastname }}"
                                           class="form-control" id="exampleInputEmail111"
                                           placeholder="{{ __('fields.lastname') }}" autocomplete="off" pattern="[\u0600-\u0605 ؐ-ؚ\u061Cـ ۖ-\u06DD ۟-ۤ ۧ ۨ ۪-ۭ ً-ٕ ٟ ٖ-ٞ ٰ ، ؍ ٫ ٬ ؛ ؞ ؟ ۔ ٭ ٪ ؉ ؊ ؈ ؎ ؏
                                        ۞ ۩ ؆ ؇ ؋ ٠۰0 ١۱1 ٢۲2 ٣۳3 ٤۴4 ٥۵5 ٦۶6 ٧۷7 ٨۸8 ٩۹9 ءٴ۽ آ أ ٲ ٱ ؤ إ ٳ ئ ا ٵ ٮ ب ٻ پ ڀ
                                        ة-ث ٹ ٺ ټ ٽ ٿ ج ڃ ڄ چ ڿ ڇ ح خ ځ ڂ څ د ذ ڈ-ڐ ۮ ر ز ڑ-ڙ ۯ س ش ښ-ڜ ۺ ص ض ڝ ڞ
                                        ۻ ط ظ ڟ ع غ ڠ ۼ ف ڡ-ڦ ٯ ق ڧ ڨ ك ک-ڴ ػ ؼ ل ڵ-ڸ م۾ ن ں-ڽ ڹ ه ھ ہ-ۃ ۿ ەۀ وۥ ٶ
                                        ۄ-ۇ ٷ ۈ-ۋ ۏ ى يۦ ٸ ی-ێ ې ۑ ؽ-ؿ ؠ ے ۓ \u061D]+" required>
                                    <div class="invalid-feedback">این فیلد نباید خالی باشد. (از حروف فارسی استفاده کنید)
                                    </div>
                                </div>
                            </div>
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail111"> {{ __('fields.user_name') }}</label>
                                    <input type="text" name="user_name" value="{{ $user->user_name }}"
                                           class="form-control" id="exampleInputEmail111"
                                           placeholder="{{ __('fields.user_name') }}" autocomplete="off" disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputState"> {{ __('fields.role.name') }}</label>
                                    <select id="inputState" class="form-control" name="role" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                    @if ($user->role_id == $role->id) selected @endif>{{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">نام نقش را انتخاب کنید.</div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail111"> {{ __('fields.status') }}</label>
                                    <select id="inputState" class="form-control" name="status" required>
                                        <option value="active" @if ($user->status == 'active') selected @endif>فعال
                                        </option>
                                        <option value="inactive" @if ($user->status != 'active') selected @endif>غیر
                                            فعال
                                        </option>
                                    </select>
                                    <div class="invalid-feedback">وضعیت نقش را انتخاب کنید.</div>
                                </div>
                            </div>
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="warning_message"
                                               @if($user->warning_message == true)
                                               checked
                                               @endif
                                               class="custom-control-input" id="warning_message">
                                        <label class="custom-control-label" for="warning_message">ارسال پیامک
                                            هشدار</label>
                                    </div>
                                </div>
                                <div
                                 @if($user->warning_message == true)
                                 class="form-group col-md-6"
                                 @else
                                 class="form-group col-md-6 d-none"
                                 @endif
                                     id="mobile_div">
                                    <label for="mobile"> {{ __('fields.mobile') }}</label>
                                    <input type="text" name="mobile" value="{{ $user->mobile }}"
                                           class="form-control" id="mobile"
                                           placeholder="{{ __('fields.mobile') }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2"
                                    onclick="return confirm('در صورت تایید تغییرات مورد نظر ثبت شود؟');">ویرایش
                            </button>
                            <a href="{{ route('user.index') }}" class="btn btn-danger">انصراف</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        $("#warning_message").change(function () {
            if (this.checked) {
                $("#mobile_div").removeClass("d-none");
            } else {
                $("#mobile_div").addClass("d-none");
            }
        });
    </script>
    <!-- These plugins only need for the run this page -->
    <script src="{{ asset('js/default-assets/basic-form.js') }}"></script>
@endsection
