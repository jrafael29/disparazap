<?php

namespace App\Jobs;

use App\Models\FlowToSent;
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
        FlowToSent::with(['instance', 'sent', 'user'])
            ->whereHas('user', function ($userQuery) {
                $userQuery
                    ->with('wallet')
                    ->whereHas('wallet', function ($creditQuery) {
                        $creditQuery->where('credit', '>', 0);
                    });
            })
            ->whereHas('instance', function ($query) {
                $query
                    ->where('available_at', '<', now()->subSecond())
                    ->where('active', 1)
                    ->where('online', 1);
            })
            ->whereHas('sent', function ($query) {
                $query->where('paused', 0);
            })
            ->where('to_sent_at', '<', now()->subSecond())
            ->where('sent', 0)
            ->get()
            ->unique('instance_id')
            ->each(function (FlowToSent $flowToSent) {
                VerifyFlowToSentJob::dispatch($flowToSent)->onQueue('high');
            });
    }
}

// FlowToSent::with(['instance'])->whereHas('instance', function ($query) {$query->where('available_at', '<', now()->subSecond())->where('active', 1)->where('online', 1);})->whereHas('sent', function ($query) {$query->where('paused', 0);})->where('to_sent_at', '<', now()->subSecond())->where('sent', 0)->get()->unique('instance_id')->each(function (FlowToSent $flowToSent) {VerifyFlowToSentJob::dispatch($flowToSent);});
