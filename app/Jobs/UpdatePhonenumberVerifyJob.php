<?php

namespace App\Jobs;

use App\Models\PhonenumberCheck;
use App\Models\VerifiedPhonenumberCheck;
use App\Service\PhonenumberService;
use App\Service\UserContactService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
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
        UserContactService $userContactService,
        PhonenumberService $phonenumberService
    ): void {
        try {
            $phonenumber = $this->phonenumber;
            $exists = $this->exists;
            Log::info('init UpdatePhonenumberVerifyJob', [
                'phonenumber' => $phonenumber,
                'exists' => $exists
            ]);
            $phonenumberService->updatePhonenumberExistence($phonenumber, $exists);
            if ($exists) {
                // se o numero existir no whatsapp, salva ele no contato do usuario.
                $userContactService->createUserContact(
                    userId: $this->check->user_id,
                    description: 'Número existente no WhatsApp.',
                    phonenumber: $phonenumber
                );
            }
            // verifica se a checagem acabou.
            $this->verifyIfCheckIsDone(check: $this->check);
            Log::info('end UpdatePhonenumberVerifyJob');
        } catch (\Exception $e) {
            Log::error("error UpdatePhonenumberVerifyJob", [
                'message' => $e->getMessage()
            ]);
        }
    }

    private function verifyIfCheckIsDone(PhonenumberCheck $check)
    {
        $checkPendingVerifiesCount = VerifiedPhonenumberCheck::query()
            ->where('check_id', $check->id)
            ->where('done', 0)
            ->count();
        if ($checkPendingVerifiesCount < 1) {
            $check->done = 1;
            $check->save();
        }
    }
}
