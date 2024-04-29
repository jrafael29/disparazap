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
        UserContactService $userContactService
    ): void {
        $phonenumber = $this->phonenumber;
        $exists = $this->exists;
        Log::info('init UpdatePhonenumberVerifyJob', [
            'phonenumber' => $phonenumber,
            'exists' => $exists
        ]);
        DB::beginTransaction();
        try {
            $ddiAndDddDigits = substr($phonenumber, 0, 4);
            $phoneDigits = Phonenumber::lastEightDigits($phonenumber);

            $verifiedPhonenumber = VerifiedPhonenumber::query()
                ->where('phonenumber', 'like',  $ddiAndDddDigits . '%')
                ->where('phonenumber', 'like', '%' . $phoneDigits)
                ->first();

            if (!$verifiedPhonenumber) {
                // o numero não está na tabela de numeros verificados
                Log::warning("verified phonenumber doesnot exists or is already verified UpdatePhonenumberVerifyJob data", [
                    'check' => $this->check->id,
                    'phonenumber' => $phonenumber,
                    'exists' => $exists
                ]);
                return;
            }

            if (!$verifiedPhonenumber->verified) {
                // numero de telefone nao foi verificado ainda
                $verifiedPhonenumber->verified = 1;
                $verifiedPhonenumber->isOnWhatsapp = $exists;
            } else {
                // numero de telefone ja estava verificado
            }

            $verifiedPhonenumberCheck = VerifiedPhonenumberCheck::query()
                ->where('verify_id', $verifiedPhonenumber->id)
                ->first();

            if (!$verifiedPhonenumberCheck) {
                Log::warning("verified phonenumber check doesnot exists on table UpdatePhonenumberVerifyJob data", [
                    'check' => $this->check->id,
                    'phonenumber' => $phonenumber,
                    'exists' => $exists,
                    'verify_id' => $verifiedPhonenumber->id
                ]);
                return;
            }

            if (!$verifiedPhonenumberCheck->done) {
                // checagem do telefone não foi concluida
                $verifiedPhonenumberCheck->done = 1;
            } else {
                // checagem do telefone foi concluida
            }

            if ($exists) {
                // se o numero existir no whatsapp, salva ele no contato do usuario.
                $userContactService->createUserContact(
                    userId: $this->check->user_id,
                    description: 'Número existente no WhatsApp.',
                    phonenumber: $phonenumber
                );
            }

            // verifica se a checagem acabou.

            $checkPendingVerifiesCount = VerifiedPhonenumberCheck::query()
                ->where('check_id', $this->check->id)
                ->where('done', 0)
                ->count();
            if ($checkPendingVerifiesCount < 1) {
                $this->check->done = 1;
            }

            $verifiedPhonenumber->save();
            $verifiedPhonenumberCheck->save();
            $this->check->save();
            DB::commit();
            Log::info('end UpdatePhonenumberVerifyJob');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("error UpdatePhonenumberVerifyJob", [
                'message' => $e->getMessage()
            ]);
        }
    }
}
