<?php

namespace App\Jobs;

use App\Helpers\Phonenumber;
use App\Models\Contact;
use App\Models\Instance;
use App\Models\PhonenumberCheck;
use App\Models\UserContact;
use App\Models\VerifiedPhonenumber;
use App\Models\VerifiedPhonenumberCheck;
use App\Service\Evolution\EvolutionChatService;
use App\Service\InstanceService;
use App\Service\UserContactService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetReadyPhonenumbersToVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     */
    const PHONENUMBERS_COUNT_PER_BATCH_TO_VERIFY = 75;
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $checkVerifies = VerifiedPhonenumberCheck::query()
                ->with(['verify'])
                ->whereHas('verify', function ($query) {
                    $query->where('verified', 0);
                })
                ->where('done', 0);
            Log::info("init GetReadyPhonenumbersToVerifyJob", [
                'verifies' => $checkVerifies->count()
            ]);
            if ($checkVerifies->count() < 1) {
            };

            $check = PhonenumberCheck::query()->findOrFail($checkVerifies->first()->check_id);

            if (!$check) {
                Log::warning("verificação não encontrada GetReadyPhonenumbersToVerifyJob", [
                    'check' => $check
                ]);
                return;
            };
            $firstInstance = Instance::query()
                ->where('available_at', '<', now()->subSecond())
                ->where('user_id', $check->user_id)
                ->where('online', 1)
                ->where('active', 1)
                ->first();

            if (!$firstInstance) {
                Log::warning("usuario nao possui instancia disponivel ou online GetReadyPhonenumbersToVerifyJob", [
                    'check' => $check,
                    'user' => $check->user,
                    'instance' => $firstInstance
                ]);
                return;
            };

            // verifica de 75 em 75 numeros...
            $numbers = [];
            $checkVerifies
                ->limit(self::PHONENUMBERS_COUNT_PER_BATCH_TO_VERIFY)
                ->get()
                ->unique('verify.phonenumber')
                ->each(function ($item) use (&$numbers) {
                    array_push($numbers, $item->verify->phonenumber);
                });
            $filteredNumbers = Phonenumber::filterUniquePhonenumbers($numbers);

            VerifyPhonenumbersExistenceJob::dispatch($firstInstance, $check, $filteredNumbers);

            DB::commit();
            Log::info("end GetReadyPhonenumbersToVerifyJob", [
                'check' => $check,
                'instanceName' => $firstInstance->name,
                'phonenumbers' => $filteredNumbers
            ]);
        } catch (\Exception $e) {
            Log::error("error: GetReadyPhonenumbersToVerifyJob", ['message' => $e->getMessage()]);
            DB::rollback();
        }
    }
}
