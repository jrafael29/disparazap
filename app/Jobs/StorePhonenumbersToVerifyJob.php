<?php

namespace App\Jobs;

use App\Models\PhonenumberCheck;
use App\Models\User;
use App\Models\VerifiedPhonenumber;
use App\Models\VerifiedPhonenumberCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StorePhonenumbersToVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phonenumbers = [];
    public User $user;
    /**
     * Create a new job instance.
     */
    public function __construct($userId, $phonenumbers = [])
    {
        $this->user = User::query()->findOrFail($userId);
        // phonenumbers can be 100000 length
        $this->phonenumbers = $phonenumbers;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->phonenumbers)) return;
        Log::info("init StorePhonenumbersToVerifyJob");
        // create check
        try {


            $check = PhonenumberCheck::query()->create([
                'user_id' => $this->user->id,
                'description' => Str::uuid()->toString()
            ]);

            foreach ($this->phonenumbers as $phonenumber) {

                $phonenumberAlreadyVerified = VerifiedPhonenumberCheck::query()
                    ->whereHas('verify', function ($query) use ($phonenumber) {
                        $query
                            ->where('phonenumber', $phonenumber)
                            ->where('verified', 1);
                    })
                    ->where('done', 1)
                    ->first();

                if ($phonenumberAlreadyVerified) {
                    VerifiedPhonenumberCheck::query()
                        ->create([
                            'check_id' => $check->id,
                            'verify_id' => $phonenumberAlreadyVerified->verify->id,
                            'done' => 1
                        ]);
                    continue;
                }

                $toVerifyPhonenumber = VerifiedPhonenumber::query()->firstOrCreate(
                    ['phonenumber' => $phonenumber],
                    ['phonenumber' => $phonenumber]
                );

                VerifiedPhonenumberCheck::query()
                    ->create([
                        'check_id' => $check->id,
                        'verify_id' => $toVerifyPhonenumber->id,
                        'done' => 0
                    ]);
            }
            Log::info("end StorePhonenumbersToVerifyJob");
        } catch (\Exception $e) {
            //throw $th;
            Log::error("init StorePhonenumbersToVerifyJob", ['message' => $e->getMessage()]);
        }
    }
}
