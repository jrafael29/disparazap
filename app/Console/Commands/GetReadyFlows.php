<?php

namespace App\Console\Commands;

use App\Jobs\GetReadyFlowsToSentJob;
use Illuminate\Console\Command;

class GetReadyFlows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:get-ready-flows';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // GetReadyFlowsToSentJob::dispatch()->onQueue('high');
        GetReadyFlowsToSentJob::dispatch()->onQueue('default');
    }
}
