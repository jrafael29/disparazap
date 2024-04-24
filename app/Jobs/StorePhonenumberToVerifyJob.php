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
use Illuminate\Support\Facades\Log;

class StorePhonenumberToVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private PhonenumberCheck $check;
    private $phonenumber;
    /**
     * Create a new job instance.
     */
    public function __construct($check, $phonenumber)
    {
        $this->check = $check;
        $this->phonenumber = $phonenumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $phonenumber = (string) $this->phonenumber;
            Log::info("init StorePhonenumberToVerifyJob", ['phonenumber' => $phonenumber]);

            $phonenumberAlreadyVerified = VerifiedPhonenumberCheck::query()
                ->with(['verify'])
                ->whereHas('verify', function ($query) use ($phonenumber) {
                    $phone = Phonenumber::lastEightDigits($phonenumber);
                    $query
                        ->where('phonenumber', 'like', '%' . $phone . '%')
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
            Log::info("end StorePhonenumberToVerifyJob", [
                'check' => $this->check,
                'phonenumber' => $phonenumber
            ]);
        } catch (\Exception $e) {
            //throw $th;
            Log::error("error StorePhonenumberToVerifyJob", ['message' => $e->getMessage()]);
        }
    }
}
