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
use Illuminate\Support\Facades\Log;

class GetReadyPhonenumbersToVerifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        try {
            Log::info("init GetReadyPhonenumbersToVerifyJob");

            // o começo da verificação da existencia dos telefones pendentes (whatsapp).

            // para verificar um numero, eu preciso de uma instancia

            // usar a instancia online do usuario da checagem.
            $checks = PhonenumberCheck::query()
                ->with(
                    [
                        'user' => function ($userQuery) {
                            $userQuery
                                ->with(['instances', 'wallet'])
                                ->whereHas('wallet', function ($walletQuery) {
                                    $walletQuery->where('credit', '>', 0);
                                })
                                ->whereHas('instances', function ($instanceQuery) {
                                    $instanceQuery->where('online', 1);
                                });
                        },
                        'verifies' => function ($verifiesQuery) {
                            $verifiesQuery->where('verified', 0)->take(75);
                        }
                    ]
                )
                ->get()
                ->each(function ($check) {
                    if ($check)
                        GetCheckPhonenumbersToVerifyJob::dispatch($check)->onQueue('high');
                });

            // dd($checks);
            return;




            // PhonenumberCheck::query()
            //     ->with(['user', 'verifies'])
            //     ->whereHas('user', function ($userQuery) {
            //         $userQuery
            //             ->with(['wallet'])
            //             ->whereHas('wallet', function ($walletQuery) {
            //                 $walletQuery->where('credit', '>', 0);
            //             });
            //     })
            //     ->whereHas('verifies', function ($verifiesQuery) {
            //         $verifiesQuery->where('verified', 0);
            //     })
            //     // ->where('done', 0)
            //     ->get()
            //     ->each(function (PhonenumberCheck $check) {
            //         if ($check->verifies->count()) {
            //             GetCheckPhonenumbersToVerifyJob::dispatch($check)->onQueue('high');
            //         }
            //     });
            Log::info("end GetReadyPhonenumbersToVerifyJob");
        } catch (\Exception $e) {
            Log::error("error: GetReadyPhonenumbersToVerifyJob", ['message' => $e->getMessage()]);
        }
    }
}
