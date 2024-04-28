<?php

namespace App\Console\Commands;

use App\Jobs\CheckInstanceStateJob;
use Illuminate\Console\Command;

class CheckInstances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:check-instances';

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
        // CheckInstanceStateJob::dispatch()->onQueue('low');
        CheckInstanceStateJob::dispatch()->onQueue('default');
    }
}
