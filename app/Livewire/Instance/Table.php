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
use Mary\Traits\Toast;

class Table extends Component
{
    use Toast;
    public $updatedQrCodePath;
    public string $search = '';

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    private InstanceService $instanceService;


    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'description', 'label' => 'Descrição', 'class' => 'w-64', 'sortable' => false]

        ];
    }

    public function rowDecoration(): array
    {
        return [
            'text-red-500' => function (Instance $instance) {
                return !$instance->online;
            }
        ];
    }


    function deleteInstanceClick(Instance $instance)
    {
        $this->instanceService->deleteInstance($instance->name);
        $this->dispatch('instance::deleted');
    }

    function getQrClick(Instance $instance)
    {
        // solicitar um qr pro usuario
        // dd("Bora buscar");
        $this->instanceService->updateQrInstance($instance->name);
        $this->dispatch('qrcode::updated');
        $this->success("Sucesso ao gerar qrCode da instancia $instance->description");
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
    #[On("qrcode::updated")]
    public function render()
    {
        return view('livewire.instance.table',  [
            'instances' => $this->instances(),
            'headers' => $this->headers(),
            'rowDecoration' => $this->rowDecoration()
        ]);
    }
}
