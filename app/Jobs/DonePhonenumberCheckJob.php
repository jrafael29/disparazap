<?php

namespace App\Jobs;

use App\Models\PhonenumberCheck;
use App\Models\VerifiedPhonenumberCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DonePhonenumberCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct(public PhonenumberCheck $check)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            //code...
            Log::info('init DonePhonenumberCheckJob');
            $countPhonenumbersToVerify = VerifiedPhonenumberCheck::query()->where(['check_id' => $this->check->id])->count();
            $countDoneVerifies = $this->check->verifies->where('verified', 1)->count();
            if ($countPhonenumbersToVerify === $countDoneVerifies) {
                $this->check->done = 1;
                $this->check->save();
            }
            Log::info('end DonePhonenumberCheckJob', [
                'countPhonenumbersToVerify' => $countPhonenumbersToVerify,
                'countDoneVerifies' => $countDoneVerifies
            ]);
        } catch (\Exception $e) {
            Log::error("error DonePhonenumberCheckJob", ['message' => $e->getMessage()]);
            //throw $th;
        }
    }
}
