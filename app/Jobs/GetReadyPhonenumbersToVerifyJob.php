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
            Log::info("init GetReadyPhonenumbersToVerifyJob");
            PhonenumberCheck::query()
                ->with(['user'])
                ->whereHas('user', function ($userQuery) {
                    $userQuery
                        ->with(['wallet'])
                        ->whereHas('wallet', function ($walletQuery) {
                            $walletQuery->where('credit', '>', 0);
                        });
                })
                ->where('done', 0)
                ->get()
                ->each(function (PhonenumberCheck $check) {
                    if ($check->verifies->count()) {
                        GetCheckPhonenumbersToVerifyJob::dispatch($check)->onQueue('low');
                    }
                });
            Log::info("end GetReadyPhonenumbersToVerifyJob");
        } catch (\Exception $e) {
            Log::error("error: GetReadyPhonenumbersToVerifyJob", ['message' => $e->getMessage()]);
        }
    }
}
