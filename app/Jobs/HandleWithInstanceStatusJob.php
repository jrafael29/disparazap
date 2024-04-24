<?php

namespace App\Jobs;

use App\Models\Instance;
use App\Service\InstanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HandleWithInstanceStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Instance $instance)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(InstanceService $instanceService): void
    {
        try {
            $stateInstance = $instanceService->getInstanceState($this->instance->name);
            Log::info("init HandleWithInstanceStatusJob data", [
                'instanceName' => $this->instance->name,
                'instanceState' => $stateInstance
            ]);
            if ($stateInstance === false) {
                // nao existe, cria uma.
                $evolutionInstanceData = $instanceService->createEvolutionInstance(
                    instanceName: $this->instance->name,
                    phonenumber: $this->instance->phonenumber
                );
                $this->instance->online = 0;
                return;
            }
            switch ($stateInstance['data']['state']) {
                case 'connecting':
                    $this->instance->online = 0;
                    break;
                case 'open':
                    $this->instance->online = 1;
                    break;
                case 'close':
                    // se estiver fechada, desconecta;
                    $this->instance->online = 0;
                    break;
            }
            $this->instance->save();
            Log::info("end HandleWithInstanceStatusJob data", [
                'done' => true,
            ]);
        } catch (\Exception $e) {
            Log::error("error HandleWithInstanceStatusJob message", [
                'message' => $e->getMessage()
            ]);
        }
    }
}
