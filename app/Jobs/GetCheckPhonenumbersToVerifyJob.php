<?php

namespace App\Jobs;

use App\Helpers\Phonenumber;
use App\Models\Instance;
use App\Models\PhonenumberCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GetCheckPhonenumbersToVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    const PHONENUMBERS_COUNT_PER_BATCH_TO_VERIFY = 75;

    /**
     * Create a new job instance.
     */
    public function __construct(public PhonenumberCheck $check)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('init GetCheckPhonenumbersToVerifyJob data', [
                'checkId' => $this->check->id
            ]);
            //code...
            $phonenumbers = [];
            $this->check->verifies
                ->take(self::PHONENUMBERS_COUNT_PER_BATCH_TO_VERIFY)
                ->each(function ($item) use (&$phonenumbers) {
                    if (!empty($item->verify->phonenumber)) {
                        array_push($phonenumbers, $item->verify->phonenumber);
                    }
                });
            $uniquePhonenumbers = Phonenumber::filterUniquePhonenumbers($phonenumbers);
            $firstCheckUserInstance = Instance::query()
                ->where('available_at', '<', now()->subSecond())
                ->where('user_id', $this->check->user_id)
                ->where('online', 1)
                ->where('active', 1)
                ->first();
            if (empty($uniquePhonenumbers)) {
                Log::warning('uniquePhonenumbers is empty GetCheckPhonenumbersToVerifyJob data', [
                    'uniquePhonenumbers' => $uniquePhonenumbers
                ]);
            }
            VerifyPhonenumbersExistenceJob::dispatch($firstCheckUserInstance, $this->check, $uniquePhonenumbers)->onQueue('high');
            Log::info('end GetCheckPhonenumbersToVerifyJob data', [
                'phonenumbers' => $phonenumbers,
                'uniquePhonenumbers' => $uniquePhonenumbers
            ]);
        } catch (\Exception $e) {
            Log::error('error GetCheckPhonenumbersToVerifyJob message', [
                'message' => $e->getMessage()
            ]);
            //throw $th;
        }
    }
}
