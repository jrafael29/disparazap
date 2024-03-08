<?php

namespace App\Livewire\Instance;

use App\Models\Instance;
use App\Service\Evolution\EvolutionInstanceService;
use App\Service\InstanceService;
use Livewire\Component;
use Mary\Traits\Toast;

class Card extends Component
{
    use Toast;
    public Instance $instance;
    private EvolutionInstanceService $evolutionInstanceService;
    private InstanceService $instanceService;

    public ?string $profilePictureUrl = '';
    public ?string $profileName = '';
    public ?string $profileStatus = '';

    function deleteInstanceClick(Instance $instance)
    {
        $this->instanceService->deleteInstance($this->instance->name);
        $this->dispatch('instance::deleted');
        $this->redirectRoute('instance');
    }

    function mount(Instance $instance)
    {
        $this->instance = $instance;

        if ($this->instance->online) {
            $result = $this->instanceService->getInstance($this->instance->name);
            if ($result['error'] === false) {
                $this->profilePictureUrl = $result['data']['profilePictureUrl'];
                $this->profileName = $result['data']['profileName'];
                $this->profileStatus = $result['data']['profileStatus'];
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
