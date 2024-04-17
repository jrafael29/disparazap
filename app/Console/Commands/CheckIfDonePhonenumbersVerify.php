<?php

namespace App\Console\Commands;

use App\Jobs\CheckIfDonePhonenumbersVerifyJob;
use Illuminate\Console\Command;

class CheckIfDonePhonenumbersVerify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:check-if-done-phonenumbers-checks';

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
        CheckIfDonePhonenumbersVerifyJob::dispatch();
    }
}
