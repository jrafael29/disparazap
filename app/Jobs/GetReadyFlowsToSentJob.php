<?php

namespace App\Jobs;

use App\Models\FlowToSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetReadyFlowsToSentJob implements ShouldQueue
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
        FlowToSent::with(['instance'])
            ->whereHas('instance', function ($query) {
                $query->where('available_at', '<', now()->subSecond())
                    ->where('online', '1');
            })
            ->where('to_sent_at', '<', now()->subSecond())
            ->where('busy', 0)
            ->get()
            ->unique('instance_id')
            ->each(function (FlowToSent $flowToSent) {
                VerifyFlowToSentJob::dispatch($flowToSent);
            });
    }
}
