<?php

namespace App\Jobs;

use App\Models\FlowToSent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyFlowToSentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private FlowToSent $flowToSent)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("init VerifyFlowToSentJob");

        $ownerAlreadyHasBusyFlow = FlowToSent::where('instance_id', $this->flowToSent->instance_id)
            ->where('busy', 1)
            ->where('sent', 0)
            ->first();
        // se ja existir algum 'flow_to_sent' sendo enviado por essa instancia, retorna falso;
        // a instancia deve enviar 1 fluxo por vez.
        if ($ownerAlreadyHasBusyFlow) {
            Log::info("ownerAlreadyHasBusyFlow VerifyFlowToSentJob", [
                'ownerAlreadyHasBusyFlow' => $ownerAlreadyHasBusyFlow
            ]);
            return;
        };
        // SendMessageFlowToTargetJob::dispatch($this->flowToSent)->onQueue('high');
        SendMessageFlowToTargetJob::dispatch($this->flowToSent)->onQueue('default');
        // $this->flowToSent->busy = 1;
        // $this->flowToSent->save();
        Log::info("end VerifyFlowToSentJob");

        return;
    }
}
