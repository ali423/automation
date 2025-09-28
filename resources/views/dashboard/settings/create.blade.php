@extends('layouts.main')
@section('title', 'افزودن تنظیمات جدید')

@section('page_styles')

@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">افزودن تنظیمات جدید</h4>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <form method="post" action="{{ route('settings.store') }}" class="needs-validation" novalidate="">
                            @csrf
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-6">
                                    <label for="key">کلید <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('key') is-invalid @enderror" 
                                           id="key" name="key" value="{{ old('key') }}" 
                                           placeholder="مثال: vat_rate" required>
                                    @error('key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">کلید باید منحصر به فرد باشد و فقط شامل حروف، اعداد و خط تیره باشد.</small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="name">نام <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="مثال: نرخ مالیات ارزش افزوده" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row col-md-12">
                                <div class="form-group col-md-6">
                                    <label for="type">نوع <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="">انتخاب کنید</option>
                                        <option value="string" {{ old('type') == 'string' ? 'selected' : '' }}>متن</option>
                                        <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>عدد</option>
                                        <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>بله/خیر</option>
                                        <option value="json" {{ old('type') == 'json' ? 'selected' : '' }}>JSON</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="value">مقدار <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('value') is-invalid @enderror" 
                                           id="value" name="value" value="{{ old('value') }}" 
                                           placeholder="مقدار تنظیمات (برای درصد: 10، برای عدد: 1000)" required>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="description">توضیحات</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="توضیحات مربوط به این تنظیمات">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <button type="submit" class="btn btn-primary mr-2">ثبت</button>
                            <a href="{{ route('settings.index') }}" class="btn btn-danger">انصراف</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        document.getElementById('type').addEventListener('change', function() {
            const valueInput = document.getElementById('value');
            const type = this.value;
            
            if (type === 'boolean') {
                valueInput.type = 'checkbox';
                valueInput.value = '1';
            } else if (type === 'number') {
                valueInput.type = 'number';
                valueInput.step = '0.01';
            } else {
                valueInput.type = 'text';
            }
        });
    </script>
@endsection
