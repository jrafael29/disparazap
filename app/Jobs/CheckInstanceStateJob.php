<?php

namespace App\Jobs;

use App\Models\Instance;
use App\Service\InstanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class CheckInstanceStateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private InstanceService $instanceService;
    public function __construct()
    {
        $this->instanceService = App::make(InstanceService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Instance::query()->where('online', 1)->get()->each(function (Instance $instance) {
            $stateInstance = $this->instanceService->getInstanceState($instance->name);
            if ($stateInstance === false) {
                // nao existe, cria uma.
                $evolutionInstanceData = $this->instanceService->createEvolutionInstance($instance->name, $instance->phonenumber);
                $instance->online = 0;
            }

            switch ($stateInstance) {
                case 'close':
                    // se estiver fechada, desconecta;
                    $instance->online = 0;
                    break;
            }
            $instance->save();
        });
    }
}
