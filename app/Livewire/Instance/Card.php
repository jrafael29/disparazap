<?php

namespace App\Livewire\Instance;

use App\Models\Instance;
use App\Service\Evolution\EvolutionInstanceService;
use App\Service\InstanceService;
use Illuminate\Support\Facades\Cache;
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

        if ($this->instance->online && !$this->profilePictureUrl) {
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
        $profilePictureUrlCacheKey = $this->instance->id . "-instance:profilePictureUrl";
        $profileNameCacheKey = $this->instance->id . "-instance:profileName";
        $profileStatusCacheKey = $this->instance->id . "-instance:profileStatus";
        $cachedPicture = Cache::get($profilePictureUrlCacheKey);
        $cachedName = Cache::get($profileNameCacheKey);
        $cachedStatus = Cache::get($profileStatusCacheKey);
        if ($cachedPicture && $cachedName && $cachedStatus) {
            $this->profilePictureUrl = $cachedPicture;
            $this->profileName = $cachedName;
            $this->profileStatus = $cachedStatus;
        }

        return view('livewire.instance.card');
    }
}
