<?php

namespace App\Jobs;

use App\Helpers\Phonenumber;
use App\Models\Instance;
use App\Models\PhonenumberCheck;
use App\Models\VerifiedPhonenumber;
use App\Models\VerifiedPhonenumberCheck;
use App\Service\Evolution\EvolutionChatService;
use App\Service\UserContactService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifyPhonenumbersExistenceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Instance $instance,
        public PhonenumberCheck $check,
        public array $phonenumbers
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
        EvolutionChatService $evolutionChatService,
        UserContactService $userContactService
    ): void {
        try {
            $result = $evolutionChatService->checkNumbers(
                instanceName: $this->instance->name,
                numbers: $this->phonenumbers
            );
            if (empty($result)) {
                Log::warning("resultado da checagem vazio em VerifyPhonenumbersExistenceJob", [
                    'data' => $result
                ]);
                return;
            };
            Log::info("init VerifyPhonenumbersExistenceJob", [
                'data' => $result,
                'instanceName' => $this->instance->name,
                'checkId' => $this->check->id
            ]);
            DB::beginTransaction();
            foreach ($result as $phonenumber => $exists) {
                // UpdatePhonenumberVerifyJob::dispatch(
                //     $this->check,
                //     (string)$phonenumber,
                //     (bool)$exists
                // )->onQueue('high');
                UpdatePhonenumberVerifyJob::dispatch(
                    $this->check,
                    (string)$phonenumber,
                    (bool)$exists
                )->onQueue('default');
            }
            $this->instance->available_at = Carbon::now()->addSeconds(1);
            $this->instance->save();
            DB::commit();
            Log::info("end VerifyPhonenumbersExistenceJob");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("error VerifyPhonenumbersExistenceJob message", [
                'message' => $e->getMessage()
            ]);
            //throw $th;
        }
    }
}
