<?php


namespace App\Service;

use App\Helpers\ArrayHelper;
use App\Helpers\Phonenumber;
use App\Models\Instance;
use App\Models\VerifiedPhonenumber;
use App\Models\VerifiedPhonenumberCheck;
use App\Service\Evolution\EvolutionChatService;
use App\Traits\ServiceResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhonenumberService
{
    use ServiceResponseTrait;
    public function __construct(private EvolutionChatService $evolutionChatService)
    {
    }

    public function verifyPhonenumbersExistence($userId, $phonenumbers = [])
    {
        // $instances = ['2-instance-1', '2-instance-2', '2-instance-3'];
        // $instancesCount = Instance::query()->where('user_id', $userId)->count();

        // if (empty($phonenumbers) || empty($instances)) return $this->errorResponse(
        //     message: "invalid parameters"
        // );
        // // phonenumber = 100.000 length   => phonenumbers can be min 1 max N
        // // $instances = 5 length          => instaces can be min 1 max N

        // $phonenumbersLength = count($phonenumbers);
    }

    public function updatePhonenumberExistence($phonenumber, $exists)
    {
        DB::beginTransaction();
        try {

            $ddiAndDddDigits = substr($phonenumber, 0, 4);
            $phoneDigits = Phonenumber::lastEightDigits($phonenumber);

            Log::info("init update phonenumber existence", [
                'ddi and ddd' => $ddiAndDddDigits,
                'phone diigts' => $phoneDigits
            ]);

            $verifiedPhonenumber = VerifiedPhonenumber::query()
                ->where('phonenumber', 'like',  $ddiAndDddDigits . '%')
                ->where('phonenumber', 'like', '%' . $phoneDigits)
                ->first();

            if ($verifiedPhonenumber) {

                if (!$verifiedPhonenumber->verified) {
                    $verifiedPhonenumber->verified = 1;
                    $verifiedPhonenumber->isOnWhatsapp = $exists;
                }
                $verifiedPhonenumberCheck = VerifiedPhonenumberCheck::query()
                    ->where('verify_id', $verifiedPhonenumber->id)
                    ->first();
                if (!$verifiedPhonenumberCheck->done) {
                    // checagem do telefone não foi concluida
                    $verifiedPhonenumberCheck->done = 1;
                }
                $verifiedPhonenumber->save();
                $verifiedPhonenumberCheck->save();
            }
            DB::commit();
            return $this->successResponse([]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("PhonenumberService::updatePhonenumberExistence", [
                'message' => $e->getMessage()
            ]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function storeToVerify($checkId, $phonenumber)
    {
        try {
            DB::beginTransaction();
            $phonenumberAlreadyVerified = VerifiedPhonenumberCheck::query()
                ->with(['verify'])
                ->whereHas('verify', function ($query) use ($phonenumber) {
                    $ddiAndDdd = substr($phonenumber, 0, 4); // 4 primeiros numeros
                    $phone = Phonenumber::lastEightDigits($phonenumber); // 8 ultimos numeros
                    // se o numero houver 9 ou não, nao importará
                    // 5581991931921
                    // 558191931921
                    $query
                        ->where('phonenumber', 'like', $ddiAndDdd . '%')
                        ->where('phonenumber', 'like', '%' . $phone)
                        ->where('verified', 1);
                })
                ->where('done', 1)
                ->first();
            if ($phonenumberAlreadyVerified) {
                VerifiedPhonenumberCheck::create([
                    'check_id' => $checkId,
                    'verify_id' => $phonenumberAlreadyVerified->verify->id,
                    'done' => 1
                ]);
            } else {
                $toVerifyPhonenumber = VerifiedPhonenumber::firstOrCreate(
                    ['phonenumber' => $phonenumber],
                    ['phonenumber' => $phonenumber]
                );
                VerifiedPhonenumberCheck::create([
                    'check_id' => $checkId,
                    'verify_id' => $toVerifyPhonenumber->id,
                    'done' => 0
                ]);
            }
            DB::commit();
            return $this->successResponse([]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("PhonenumberService::storeToVerify", [
                'message' => $e->getMessage()
            ]);
            return $this->errorResponse($e->getMessage());
        }
    }

    public function generatePhonenumber(int $ddi, int $ddd, int $amount)
    {
        if (!$ddi || $ddd < 10 || $ddd > 99 || !$amount) {
            return false;
        }
        $phonenumbers = [];
        for ($i = 0; $i < $amount; $i++) {
            switch ($ddi) {
                case 55:
                    array_push($phonenumbers, Phonenumber::generateBrazilPhonenumber(ddd: $ddd));
                    break;
            }
        }
        return $phonenumbers;
    }
}
