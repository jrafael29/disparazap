<?php

namespace App\Jobs;

use App\Helpers\Phonenumber;
use App\Models\PhonenumberCheck;
use App\Models\VerifiedPhonenumber;
use App\Models\VerifiedPhonenumberCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StorePhonenumberToVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct(public PhonenumberCheck $check, public $phonenumber)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $phonenumber = (string) $this->phonenumber;
            Log::info("init StorePhonenumberToVerifyJob", ['phonenumber' => $phonenumber]);
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
                Log::info("phonenumber already exists StorePhonenumberToVerifyJob", [
                    'phonenumber' => $phonenumber,
                    'queryResult' => $phonenumberAlreadyVerified
                ]);
                VerifiedPhonenumberCheck::create([
                    'check_id' => $this->check->id,
                    'verify_id' => $phonenumberAlreadyVerified->verify->id,
                    'done' => 1
                ]);
            } else {
                $toVerifyPhonenumber = VerifiedPhonenumber::firstOrCreate(
                    ['phonenumber' => $phonenumber],
                    ['phonenumber' => $phonenumber]
                );
                VerifiedPhonenumberCheck::create([
                    'check_id' => $this->check->id,
                    'verify_id' => $toVerifyPhonenumber->id,
                    'done' => 0
                ]);
            }
            DB::commit();

            Log::info("end StorePhonenumberToVerifyJob", [
                'check' => $this->check,
                'phonenumber' => $phonenumber
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("error StorePhonenumberToVerifyJob", ['message' => $e->getMessage()]);
        }
    }
}
