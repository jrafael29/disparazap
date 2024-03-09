<?php

namespace App\Livewire\Flow\Sent;

use App\Models\Instance;
use App\Service\Evolution\EvolutionGroupService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Steps extends Component
{
    public array $instances_groups = [];
    public array $instances_multi_ids = [];
    public $steps = 3;
    public $step = 1;
    public $sendOptions = [
        'group-contacts' => 'Contatos de um grupo',
        'raw-text' => "Colar texto",
        'import-excel' => "Importar excel"
    ];
    public string $sendOption = '';

    public $groupsSelected;
    public $groups;

    private EvolutionGroupService $evolutionGroupService;

    public function selectSendOption($option)
    {
        $this->sendOption = $option;
        if ($option === 'group-contacts') {
            $this->getSelectedGroups();
        }
    }


    public function customValidate()
    {
        if (count($this->instances_multi_ids) === 0) {
            return false;
        }
        return true;
    }

    public function next()
    {
        if (!$this->customValidate()) return false;
        if ($this->step === $this->steps) return false;
        $this->step++;
    }
    public function prev()
    {
        if ($this->step == 1) return false;
        $this->step--;
    }

    function selectGroup($id)
    {
        $index = $this->groupsSelected->search($id); // Procurar o índice do item no array

        if ($index === false) {
            if ($this->groupsSelected->count() === 3) {
                return false;
            }
            // Se não existe, adiciona ao array
            $this->groupsSelected->push($id);
        } else {
            // Se já existe, remove do array
            $this->groupsSelected->forget($index);
        }
    }

    public function getSelectedGroups()
    {
        $fullData = [];
        foreach ($this->instances_multi_ids as $key => $id) {
            $instanceModel = Instance::query()->findOrFail($id);
            $fullData[$id] = $this->evolutionGroupService->getGroups($instanceModel->name);
        }
        $this->instances_groups = $fullData;
        // dd($fullData);
    }

    public function boot(EvolutionGroupService $evolutionGroupService)
    {
        $this->evolutionGroupService = $evolutionGroupService;
    }

    public function mount()
    {
        $this->groups = [];
        $this->groupsSelected = collect();
    }

    public function render()
    {
        $instances = Instance::query()
            ->where('user_id', Auth::user()->id)
            ->where('online', 1)
            ->get();
        return view('livewire.flow.sent.steps', [
            'instances' => $instances
        ]);
    }
}
