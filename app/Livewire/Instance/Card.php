<?php

namespace App\Livewire\Instance;

use App\Models\Instance;
use App\Service\Evolution\EvolutionInstanceService;
use App\Service\InstanceService;
use Livewire\Component;

class Card extends Component
{
    public Instance $instance;
    private EvolutionInstanceService $evolutionInstanceService;
    private InstanceService $instanceService;

    public ?string $profilePictureUrl = '';
    public ?string $profileName = '';
    public ?string $profileStatus = '';

    function deleteInstanceClick(Instance $instance)
    {
        $this->instanceService->deleteInstance($instance->name);
        $this->dispatch('instance::deleted');
    }

    function mount(Instance $instance)
    {
        $this->instance = $instance;

        if ($this->instance->online) {
            $instanceData = $this->evolutionInstanceService->getInstance($this->instance->name);

            if ($instanceData) {
                $this->profilePictureUrl = $instanceData['profilePictureUrl'];
                $this->profileName = $instanceData['profileName'];
                $this->profileStatus = $instanceData['profileStatus'];
            }
        }
    }


    function boot(
        EvolutionInstanceService $evolutionInstanceService,
        InstanceService $instanceService
    ) {
        $this->evolutionInstanceService = $evolutionInstanceService;
        $this->instanceService = $instanceService;
    }


    public function render()
    {
        return view('livewire.instance.card');
    }
}
