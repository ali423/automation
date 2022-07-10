<?php

namespace App\Jobs;

use App\Models\Commodity;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class NotifyAdminsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $commodity;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Commodity $commodity)
    {
        $this->commodity = $commodity;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $admins = User::query()->where('warning_message', true)->where('mobile', '!=', null)->get();
        foreach ($admins as $admin) {
            Http::post('https://api.kavenegar.com/v1/' . config('enums.sms_key') . '/verify/lookup.json?receptor=' . $admin->mobile . '&token=' . $this->commodity->title . '&template=warehouse');
        }
    }
}
