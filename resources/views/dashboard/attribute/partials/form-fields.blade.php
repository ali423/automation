<div class="form-row">
    <div class="form-group col-md-6">
        <label for="name">{{ __('fields.name') }}</label>
        <input type="text" name="name" value="{{ old('name', $attribute->name ?? '') }}" class="form-control" id="name" placeholder="نام ویژگی" required>
        <div class="invalid-feedback">لطفاً نام ویژگی را وارد کنید.</div>
    </div>
    <div class="form-group col-md-6">
        <label for="description">{{ __('fields.comment') }}</label>
        <textarea name="description" class="form-control" id="description" placeholder="توضیحات" rows="3">{{ old('description', $attribute->description ?? '') }}</textarea>
    </div>
</div>
