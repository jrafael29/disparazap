<?php

namespace App\Jobs;

use App\Helpers\Phonenumber;
use App\Models\PhonenumberCheck;
use App\Models\VerifiedPhonenumber;
use App\Models\VerifiedPhonenumberCheck;
use App\Service\Evolution\EvolutionChatService;
use App\Service\UserContactService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdatePhonenumberVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public PhonenumberCheck $check,
        public string $phonenumber,
        public bool $exists
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(
        UserContactService $userContactService
    ): void {
        $phonenumber = $this->phonenumber;
        $exists = $this->exists;
        Log::info('init UpdatePhonenumberVerifyJob', [
            'phonenumber' => $phonenumber,
            'exists' => $exists
        ]);

        try {
            $ddiAndDddDigits = substr($phonenumber, 0, 4);
            $phoneDigits = Phonenumber::lastEightDigits($phonenumber);
            VerifiedPhonenumber::query()
                ->where('phonenumber', 'like',  $ddiAndDddDigits . '%')
                ->where('phonenumber', 'like', '%' . $phoneDigits)
                ->update([
                    'verified' => 1,
                    'isOnWhatsapp' => $exists
                ]);
            VerifiedPhonenumberCheck::query()
                ->with(['verify'])
                ->whereHas('verify', function ($query) use ($ddiAndDddDigits, $phoneDigits) {
                    $query
                        ->where('phonenumber', 'like',  $ddiAndDddDigits . '%')
                        ->where('phonenumber', 'like', '%' . $phoneDigits);
                })
                ->update([
                    'done' => 1
                ]);
            if ($exists) {
                $userContactService->createUserContact(
                    userId: $this->check->user_id,
                    description: '',
                    phonenumber: $phonenumber
                );
            }
            Log::info('end UpdatePhonenumberVerifyJob');
        } catch (\Exception $e) {
            Log::error("error UpdatePhonenumberVerifyJob", [
                'message' => $e->getMessage()
            ]);
        }
    }
}
