{{-- Action Buttons Partial for Withdrawal Requests --}}
<div class="row">
    <div class="col-md-6 mb-1 mb-md-0">
        @if ($request->status == 'awaiting_approval')
            <a href="{{ route('approval.withdrawal.form', $request) }}"
               class="btn btn-primary px-1">تایید درخواست</a>
        @endif
        @if (auth()->user()->role->havePermission('cancel_withdrawal') && in_array($request->status, ['approvaled', 'done']))
            <a href="{{ route('sales-return.withdrawal.form', $request) }}" class="btn btn-info px-1">برگشت از فروش</a>
            <a href="{{ route('cancel.withdrawal.form', $request) }}" class="btn btn-warning px-1">لغو کامل درخواست</a>
        @endif
    </div>
    <div class="col-md-6 text-md-right">
        @if ($request->status == 'awaiting_approval')
            <a href="{{ route('reject.withdrawal', $request) }}" class="btn btn-danger px-1">رد
                درخواست</a>
        @endif
    </div>
</div>
