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

class GetReadyPhonenumbersToVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private EvolutionChatService $evolutionChatService;
    private UserContactService $userContactService;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->evolutionChatService = App::make(EvolutionChatService::class);
        $this->userContactService = App::make(UserContactService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();
            // instancia do cara
            $checkVerifies = VerifiedPhonenumberCheck::query()
                ->with(['verify'])
                ->whereHas('verify', function ($query) {
                    $query->where('verified', 0);
                })
                ->where('done', 0);


            if ($checkVerifies->count() < 1) return;

            $check = PhonenumberCheck::query()->findOrFail($checkVerifies->first()->check_id);


            if (!$check) return;
            $firstInstance = Instance::query()
                ->where('available_at', '<', now()->subSecond())
                ->where('user_id', $check->user_id)
                ->where('online', 1)
                ->first();
            if (!$firstInstance) return;
            //numeros a serem verificados
            $numbers = [];
            $checkVerifies
                ->limit(75)
                ->get()
                ->unique('verify.phonenumber')
                ->each(function ($item) use (&$numbers) {
                    array_push($numbers, $item->verify->phonenumber);
                });
            // check 
            $firstInstance->available_at = Carbon::now()->addSeconds(5);
            $firstInstance->save();
            $filteredNumbers = Phonenumber::filterUniquePhonenumbers($numbers);
            // dd($numbers, $filteredNumbers);
            $result = $this->evolutionChatService->checkNumbers(
                instanceName: $firstInstance->name,
                numbers: $filteredNumbers
            );
            if (empty($result)) return;
            foreach ($result as $phonenumber => $exists) {
                $phonenumberWithoutDDs = substr($phonenumber, -8);
                VerifiedPhonenumber::query()
                    ->where('phonenumber', 'like', '%' . $phonenumberWithoutDDs)
                    ->update([
                        'verified' => 1,
                        'isOnWhatsapp' => $exists
                    ]);
                VerifiedPhonenumberCheck::query()
                    ->with(['verify'])
                    ->whereHas('verify', function ($query) use ($phonenumberWithoutDDs) {
                        $query->where('phonenumber', '%' . $phonenumberWithoutDDs);
                    })
                    ->update([
                        'done' => 1
                    ]);

                if ($exists) {
                    $this->userContactService->createUserContact(
                        userId: $check->user_id,
                        description: '',
                        phonenumber: $phonenumber
                    );
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
        }
    }
}
