<?php

namespace App\Livewire\Flow\Sent;

use App\Models\Instance;
use App\Service\Evolution\EvolutionGroupService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Steps extends Component
{
    private int $max_groups_selected_allowed = 15;
    // if want update the value, must be updated both values
    // for security because this props is visible in frontend;
    public int $public_max_groups_selected_allowed = 15;

    public array $instances_groups = []; // grupos de todas as instancias selecionadas.
    public array $instances_multi_ids = []; // todas as instancias selecionadas.
    public $sendOptions = [
        'group-contacts' => 'Contatos de um grupo',
        'raw-text' => "Colar texto",
        // 'import-excel' => "Importar excel"
    ]; // opções de "alvos"
    public $steps = 4; // quantidade de passos no
    public $step = 1; // step atual
    public $delay = 21; // delay entre um chat e outro (step de agendamento)
    public string $sendOption = ''; // opção de envio (step de alvos)

    public $groupsSelected; // grupos do whatsapp selecionados (step de alvos / contatos de um grupo)
    public $groups; // todos os grupos do whatsapp.

    public $rawText = ''; // caso o usuario selecione texto cru (step de alvos / colar texto)

    public $groupsParticipantsPhonenumber = [];

    private EvolutionGroupService $evolutionGroupService;

    public function selectSendOption($option)
    {
        $this->sendOption = $option;
        if ($option === 'group-contacts') {
            $this->getSelectedInstancesGroups();
        }
    }


    public function customValidate()
    {
        if (count($this->instances_multi_ids) === 0) {
            return false;
        }
        return true;
    }

    public function getPhonenumberFromParticipant($participant = [], $ddi = 0)
    {
        // Se $ddi for 0, retornar todos os números
        if ($ddi == 0) {
            if (!empty($participant['id'])) {
                // Extrair o número de telefone do ID
                $number = explode('@', $participant['id'])[0];
                return $number;
            }
            return false;
        }

        // Se $ddi for 55, filtrar apenas os números brasileiros
        if ($ddi == 55) {
            if (!empty($participant['id'])) {
                // Extrair o número de telefone do ID
                $number = explode('@', $participant['id'])[0];
                // Verificar se o número começa com 55
                if (preg_match('/^55\d{0,11}$/', $number)) {
                    return $number;
                }
            }
            return false;
        }

        // Caso $ddi seja diferente de 0 e 55, retornar falso
        return false;
    }

    public function getGroupsParticipantsPhonenumber($groups = [], $ddi = 0)
    {
        $groupsParticipantsNumber = [];
        foreach ($groups as $group) {
            $groupParts = explode(':', $group);
            $groupJid = $groupParts[0];
            $instanceId = $groupParts[1];
            $instance = Instance::query()->find($instanceId);
            $groupParticipants = $this->evolutionGroupService->getParticipantsByJid($instance->name, $groupJid);
            // dd($groupParticipants['data']);

            $phoneNumbers = [];
            foreach ($groupParticipants['data'] as $groupId => $participants) {
                $numbers = [];
                foreach ($participants as $participant) {
                    $phonenumber = $this->getPhonenumberFromParticipant($participant, $ddi);
                    if ($phonenumber)
                        $numbers[] = $phonenumber;
                }
                $phoneNumbers[$groupId] =  $numbers;
            }
            $groupsParticipantsNumber[] = $phoneNumbers;
        }
        return $groupsParticipantsNumber;
    }

    public function validateTarget()
    {
        switch ($this->sendOption) {
            case 'raw-text':
                dd($this->rawText);
                break;
            case 'group-contacts':
                $groupsPhonenumber = $this->getGroupsParticipantsPhonenumber($this->groupsSelected, 55);
                $this->groupsParticipantsPhonenumber = array_values($groupsPhonenumber);
                return true;
                break;
            default:
                return false;
        }
    }

    public function next()
    {
        if ($this->step === 2) if (!$this->validateTarget()) return false;
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
            if ($this->groupsSelected->count() === $this->max_groups_selected_allowed) {
                return false;
            }
            // Se não existe, adiciona ao array
            $this->groupsSelected->push($id);
        } else {
            // Se já existe, remove do array
            $this->groupsSelected->forget($index);
        }
    }

    public function getSelectedInstancesGroups()
    {
        $fullData = [];
        foreach ($this->instances_multi_ids as $key => $id) {
            $instanceModel = Instance::query()->findOrFail($id);
            $fullData[$id] = $this->evolutionGroupService->getGroups($instanceModel->name);
        }
        $this->instances_groups = $fullData;
    }

    public function handleRawTextChange()
    {
        dd($this->rawText);
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
        $instancesModel = Instance::query()
            ->where('user_id', Auth::user()->id)
            ->where('online', 1)
            ->get();

        // for display in choices component.
        $instances = collect($instancesModel)->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->description
            ];
        });

        return view('livewire.flow.sent.steps', [
            'instances' => $instances
        ]);
    }
}
