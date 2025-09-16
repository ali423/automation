{{-- Action Buttons Partial for Withdrawal Requests --}}
<div class="row">
    <div class="col-md-6 mb-1 mb-md-0">
        @if ($request->status == 'awaiting_approval')
            <a href="{{ route('approval.withdrawal', $request) }}"
               class="btn btn-primary px-1">تایید درخواست</a>
        @endif
    </div>
    <div class="col-md-6 text-md-right">
        @if ($request->status == 'awaiting_approval')
            <a href="{{ route('reject.withdrawal', $request) }}" class="btn btn-danger px-1">رد
                درخواست</a>
        @endif
    </div>
</div>
