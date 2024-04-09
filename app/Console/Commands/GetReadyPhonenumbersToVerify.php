<?php

namespace App\Console\Commands;

use App\Jobs\GetReadyPhonenumbersToVerifyJob;
use Illuminate\Console\Command;

class GetReadyPhonenumbersToVerify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:get-ready-phonenumbers-to-verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obter todos os números de telefone a ser verificados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        GetReadyPhonenumbersToVerifyJob::dispatch();
    }
}
