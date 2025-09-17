{{-- Action Buttons Partial for Production Requests --}}
<div class="row mt-3">
    <div class="col-md-6 mb-1 mb-md-0">
        @if($request->is_editable)
            <a href="{{ route('production-request.edit', $request) }}" class="btn btn-primary">ویرایش</a>
        @endif
        @if($request->is_deletable)
            <form method="post" action="{{ route('production-request.destroy', $request) }}" class="d-inline w-50">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('آیا از حذف این درخواست مطمئن هستید؟');">حذف</button>
            </form>
        @endif
    </div>
    <div class="col-md-6 text-md-right">
        @if($request->can_be_approved)
            <a href="{{ route('approval.production', $request) }}" class="btn btn-success px-2">تایید درخواست</a>
        @endif
        @if($request->can_be_rejected)
            <a href="{{ route('reject.production', $request) }}" class="btn btn-warning px-2">رد درخواست</a>
        @endif
        <a href="{{ route('activity.index', [
            'object_id' => $request->id,
            'object_type' => class_basename($request),
        ]) }}"
            class="btn btn-dfprimary px-1 px-md-4 m-md-0">تاریخچه تغییرات</a>
        <a href="{{ route('production-request.index') }}" class="btn btn-secondary px-2 px-md-4 m-md-0">بازگشت</a>
    </div>
</div>
