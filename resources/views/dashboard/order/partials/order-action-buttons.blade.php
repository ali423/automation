<div class="row">
    <div class="col-md-6">
        @if($order->status !== 'done')
            @if(isset($showEdit) && $showEdit)
                <a href="{{ route('order.edit', $order) }}" class="btn btn-primary">ویرایش</a>
            @endif
            @if(isset($showDelete) && $showDelete)
                <form method="post" action="{{ route('order.destroy', $order) }}" class="d-inline w-50">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('آیا از حذف این سفارش مطمئن هستید؟');">حذف سفارش</button>
                </form>
            @endif
        @else
            <span class="text-muted">سفارش تحویل شده - امکان ویرایش و حذف وجود ندارد</span>
        @endif
    </div>
    <div class="col-md-6 text-md-right">
        @if($order->status !== 'done' && isset($showConfirm) && $showConfirm)
            <a href="{{ route('order.confirm', $order) }}" class="btn btn-success">تحویل سفارش</a>
        @endif
    </div>
</div>
