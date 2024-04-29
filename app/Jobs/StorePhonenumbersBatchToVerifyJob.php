<?php

namespace App\Jobs;

use App\Helpers\Phonenumber;
use App\Models\PhonenumberCheck;
use App\Models\VerifiedPhonenumber;
use App\Models\VerifiedPhonenumberCheck;
use App\Service\PhonenumberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StorePhonenumbersBatchToVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public PhonenumberCheck $check, public $phonenumbers)
    {
        //
    }

    /**
     * Execute the job.
     */

    public function handle(PhonenumberService $phonenumberService): void
    {
        $phonenumbers = $this->phonenumbers;
        if (empty($phonenumbers)) {
            return;
        }

        foreach ($phonenumbers as $phonenumber) {
            $phonenumberService->storeToVerify(
                checkId: $this->check->id,
                phonenumber: (string) $phonenumber
            );
        }
    }
}
