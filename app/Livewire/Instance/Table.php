<?php

namespace App\Livewire\Instance;

use App\Models\Instance;
use App\Repository\InstanceRepository;
use App\Service\Evolution\EvolutionInstanceService;
use App\Service\InstanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\On;
use Livewire\Component;

class Table extends Component
{
    public string $search = '';

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    private InstanceService $instanceService;


    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'description', 'label' => 'Descrição', 'class' => 'w-64', 'sortable' => false],
        ];
    }

    function deleteInstanceClick(Instance $instance)
    {
        $this->instanceService->deleteInstance($instance->name);
        $this->dispatch('instance::deleted');
    }

    function getQrClick()
    {
        // solicitar um qr pro usuario
        dd("Bora buscar");
    }

    function updateQrClick()
    {
        // atualizar o qrCode
        dd("Bora atualizar");
    }


    public function instances()
    {
        $userAuthId = Auth::user()->id;
        $instances = Instance::query()->where('user_id', $userAuthId)->get();

        $sortedInstances = $instances->sortBy($this->sortBy);
        if ($this->search) {
            $filteredInstances = $sortedInstances->filter(function ($instance) {
                return stripos($instance->name, $this->search) !== false;
            });
            return $filteredInstances;
        }
        return $sortedInstances;
    }


    function boot(
        InstanceService $instanceService
    ) {

        $this->instanceService = $instanceService;
    }

    #[On("instance::deleted")]
    #[On("instance::created")]
    public function render()
    {
        return view('livewire.instance.table',  [
            'instances' => $this->instances(),
            'headers' => $this->headers()
        ]);
    }
}
