{{-- Comments Partial for Withdrawal Requests --}}
@if(isset($comments) && $comments->count() > 0)
    @foreach ($comments as $comment)
        <div class="form-group mb-20">
            <label for="comment"> {{ $comment->user ? $comment->user->full_name : 'کاربر نامشخص' }} در تاریخ :
                {{ \Morilog\Jalali\CalendarUtils::strftime('Y/m/d H:i:s', strtotime($comment->created_at)) }}</label>
            <textarea class="form-control rounded-0 form-control-md" name="comment" id="comment"
                      rows="6" disabled>{{ $comment->body }}</textarea>
        </div>
    @endforeach
@endif
