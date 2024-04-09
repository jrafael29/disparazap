<?php

namespace App\Jobs;

use App\Models\VerifiedPhonenumber;
use App\Models\VerifiedPhonenumberCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetReadyPhonenumbersToVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        // busca todos numeros e verifica
        try {
            $checkVerifies = VerifiedPhonenumberCheck::query()
                ->with(['verify'])
                ->whereHas('verify', function ($query) {
                    $query->where('verified', 0);
                })
                ->where('done', 0)
                ->get();
            dd($checkVerifies);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        // $phonenumbersToVerify = VerifiedPhonenumber::query()
        //     ->where('verified', 0)
        //     ->first();


    }
}
