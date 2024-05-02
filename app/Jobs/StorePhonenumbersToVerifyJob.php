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
    public PhonenumberCheck $check;
    /**
     * Create a new job instance.
     */
    public function __construct($userId, $checkId, $phonenumbers = [])
    {
        $this->user = User::find($userId);
        $this->check = PhonenumberCheck::find($checkId);
        $this->phonenumbers = $phonenumbers;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->phonenumbers)) return;
        Log::info("init StorePhonenumbersToVerifyJob");
        try {
            // salvar de 15 em 15
            // $chunks = array_chunk($this->phonenumbers, 15);
            // foreach ($chunks as $phonenumbers) {
            //     // $chunk be = ["5599", "5599"]
            //     StorePhonenumbersBatchToVerifyJob::dispatch(
            //         $this->check,
            //         $phonenumbers
            //     );
            // }

            // salvar de 1 em 1
            foreach ($this->phonenumbers as $phonenumber) {
                // StorePhonenumberToVerifyJob::dispatch($this->check, $phonenumber)->onQueue('low');
                StorePhonenumberToVerifyJob::dispatch($this->check, $phonenumber)->onQueue('default');
            }
            Log::info("end StorePhonenumbersToVerifyJob", [
                'phonenumbers' => $this->phonenumbers,
                'check' => $this->check
            ]);
        } catch (\Exception $e) {
            Log::error("error StorePhonenumbersToVerifyJob", ['message' => $e->getMessage()]);
        }
    }
}
