<div class="form-row m-3">
    <div class="form-group col">
        <label for="customer_id">{{ __('fields.customer')}}</label>
        <select id="customer_id" class="form-control" name="customer_id" required>
            <option value="">انتخاب کنید</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" 
                    @if(isset($order) && $order->customer_id == $customer->id) selected @endif
                >{{$customer->name}}</option>
            @endforeach
        </select>
        <div class="invalid-feedback">
            {{ __('fields.customer')}} را انتخاب کنید
        </div>
    </div>
</div>
<div class="form-row m-3">
    <div class="form-group col">
        <label for="deadline">{{ __('fields.deadline') }}</label>
        <input type="text" name="deadline" id="deadline" class="form-control usage" 
               autocomplete="off" required="" 
               value="{{ isset($order) ? $order->deadline : '' }}">
        <div class="invalid-feedback">
            لطفاً {{  __('fields.deadline') }} را وارد کنید.
        </div>
    </div>
</div><div class="form-row m-3">
    <div class="form-group col">
        <label for="credit_validity_days">مدت اعتبار (روز)</label>
        <input type="number" name="credit_validity_days" id="credit_validity_days" class="form-control" 
               min="1" value="{{ isset($order) && $order->credit_validity_days ? $order->credit_validity_days : '' }}">
        <div class="invalid-feedback">
            مدت اعتبار را به روز وارد کنید
        </div>
    </div>
</div>