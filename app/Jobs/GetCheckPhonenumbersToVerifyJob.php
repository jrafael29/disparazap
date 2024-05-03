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
            // $phonenumbers = [];
            $phonenumbers = $this->check->verifies
                ->where('verified', 0)
                ->take(self::PHONENUMBERS_COUNT_PER_BATCH_TO_VERIFY)
                ->pluck('phonenumber');
            if (empty($phonenumbers)) {
                Log::warning('empty phonenumbers GetCheckPhonenumbersToVerifyJob data', [
                    'check' => $this->check->id,
                    'phonenumbers' => $phonenumbers
                ]);
                return;
            }

            $firstCheckUserInstance = Instance::query()
                ->where('available_at', '<', now()->subSecond())
                ->where('user_id', $this->check->user_id)
                ->where('online', 1)
                ->where('active', 1)
                ->first();

            if (!$firstCheckUserInstance) {
                Log::warning('user has not valid instance GetCheckPhonenumbersToVerifyJob data', [
                    'check' => $this->check->id,
                    'instance' => $firstCheckUserInstance
                ]);
                return;
            }


            // VerifyPhonenumbersExistenceJob::dispatch($firstCheckUserInstance, $this->check, $phonenumbers)->onQueue('high');
            VerifyPhonenumbersExistenceJob::dispatch(
                $firstCheckUserInstance,
                $this->check,
                $phonenumbers->toArray()
            )->onQueue('default');

            Log::info('end GetCheckPhonenumbersToVerifyJob data', [
                'phonenumbers' => $phonenumbers,
                'uniquePhonenumbers' => $phonenumbers
            ]);
        } catch (\Exception $e) {
            Log::error('error GetCheckPhonenumbersToVerifyJob message', [
                'message' => $e->getMessage()
            ]);
            //throw $th;
        }
    }
}
