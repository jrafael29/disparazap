<?php

namespace App\Jobs;

use App\Models\PhonenumberCheck;
use App\Models\VerifiedPhonenumberCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DonePhonenumberCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private PhonenumberCheck $check;
    /**
     * Create a new job instance.
     */
    public function __construct($checkId)
    {
        $this->check = PhonenumberCheck::query()->findOrFail($checkId);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $countPhonenumbersToVerify = VerifiedPhonenumberCheck::query()->where([
            'check_id' => $this->check->id
        ])->count();
        $countDoneVerifies = $this->check->verifies->where('verified', 1)->count();
        if ($countPhonenumbersToVerify === $countDoneVerifies) {
            $this->check->done = 1;
            $this->check->save();
            return;
        }
    }
}
