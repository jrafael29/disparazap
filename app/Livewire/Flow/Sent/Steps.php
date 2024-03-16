<?php

namespace App\Livewire\Flow\Sent;

use App\Models\FlowToSent;
use App\Models\Instance;
use App\Models\MessageFlow;
use App\Service\Evolution\EvolutionChatService;
use App\Service\Evolution\EvolutionGroupService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

class Steps extends Component
{
    use Toast;
    private int $max_groups_selected_allowed = 3;
    // if want update the value, must be updated both values
    // for security because this props is visible in frontend;
    public int $public_max_groups_selected_allowed = 3;

    public array $selectedInstancesGroups = []; // grupos de todas as instancias selecionadas.
    public array $selectedInstances = []; // todas as instancias selecionadas.
    public $sendOptions = [
        'group-contacts' => 'Contatos de um grupo',
        'raw-text' => "Colar texto",
        // 'import-excel' => "Importar excel"
    ]; // opções de "alvos"
    public $steps = 5; // quantidade de passos no
    public $step = 1; // step atual
    public $delay = 21; // delay entre um chat e outro (step de agendamento)
    public $minDelay = 10;
    public $maxDelay = 30;
    public string $sendOption = ''; // opção de envio (step de alvos)

    #[Validate('required')]
    public $toSendDate = '';

    public $groupsSelected; // grupos do whatsapp selecionados (step de alvos / contatos de um grupo)
    // public $groups; // todos os grupos do whatsapp.
    public MessageFlow $flow;

    public $rawText = ''; // caso o usuario selecione texto cru (step de alvos / colar texto)
    public $rawPhonenumbers = [];
    public $groupsParticipantsPhonenumber = []; // numero dos participantes dos grupos selecionados

    public $phonenumbers = [];

    public $hours = '';
    public $minutes = '';
    public $seconds = '';

    private EvolutionGroupService $evolutionGroupService;
    private EvolutionChatService $evolutionChatService;

    public function selectSendOption($option)
    {
        $this->sendOption = $option;
        if ($option === 'group-contacts') {
            $this->getSelectedInstancesGroups();
        }
    }

    public function customValidate()
    {
        if (count($this->selectedInstances) === 0) {
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
                $phonenumbers = $this->getPhonenumbersFromRawText($this->rawText);
                $uniqPhonenumbers = array_values(array_unique($phonenumbers));

                $firstInstanceName = Instance::find($this->selectedInstances[0])?->name;

                $numbersExistence = $this->evolutionChatService->checkNumbersExistence(
                    numbers: $uniqPhonenumbers,
                    instanceName: $firstInstanceName
                );

                $this->phonenumbers = $numbersExistence;
                // verificar se os numeros são validos

                // $this->phonenumbers = $uniqPhonenumbers
                return true;
                break;
            case 'group-contacts':
                $groupsPhonenumber = $this->getGroupsParticipantsPhonenumber(
                    groups: $this->groupsSelected,
                    ddi: 55
                );
                $this->groupsParticipantsPhonenumber = array_values($groupsPhonenumber);
                $this->phonenumbers = $this->getPhonenumbersFromGroupsParticipants($this->groupsParticipantsPhonenumber);
                return true;
                break;
            default:
                return false;
        }
    }

    public function next()
    {
        if ($this->step === 2) {
            if (!$this->validateTarget())
                return false;
        }
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
        foreach ($this->selectedInstances as $key => $id) {
            $instanceModel = Instance::query()->findOrFail($id);
            $fullData[$id] = $this->evolutionGroupService->getGroups($instanceModel->name);
        }
        $this->selectedInstancesGroups = $fullData;
    }

    public function getPhonenumbersFromRawText($rawText)
    {
        $numbers = explode("\n", $rawText);
        $phonenumbers = array_values($numbers);
        return $phonenumbers;
    }
    public function getPhonenumbersFromGroupsParticipants($groupsParticipantsPhonenumber)
    {
        $numbers = [];
        // dd($this->groupsParticipantsPhonenumber);
        foreach ($groupsParticipantsPhonenumber as $groups) {
            foreach ($groups as $groupJid => $participants) {
                foreach ($participants as $participantNumber)
                    array_push($numbers, $participantNumber);
            }
        }
        return $numbers;
    }

    function getTotalDuration()
    {
        if (count($this->selectedInstances) > 0 && count($this->phonenumbers) > 0) {
            $total_minutes = ((30 + $this->delay) * count($this->phonenumbers) / count($this->selectedInstances)) / 60;
            $total_seconds = $total_minutes * 60;
            $this->hours = floor($total_seconds / 3600);
            $this->minutes = floor(($total_seconds % 3600) / 60);
            $this->seconds = $total_seconds % 60;
        }
    }

    public function handleFinalizeClick()
    {
        $this->validate();

        $numbers = array_keys($this->phonenumbers);
        $instances = $this->selectedInstances;

        $numbersPerInstance = count($this->phonenumbers) / count($this->selectedInstances);
        $allInstancesPhonenumbers = [];
        $offset = 0;
        foreach ($instances as $index => $instance) {
            if ($index + 1 === count($instances))
                $allInstancesPhonenumbers[$instance] = array_slice($numbers, $offset, $numbersPerInstance + 1);
            else
                $allInstancesPhonenumbers[$instance] = array_slice($numbers, $offset, $numbersPerInstance);

            $offset = $offset + $numbersPerInstance;
        }

        foreach ($allInstancesPhonenumbers as $instanceId => $phonenumbers) {
            foreach ($phonenumbers as $phonenumber) {

                FlowToSent::query()->create([
                    'user_id' => Auth::user()->id,
                    'flow_id' => $this->flow->id,
                    'instance_id' => $instanceId,
                    "to" => $phonenumber,
                    "to_sent_at" => $this->toSendDate,
                    'delay_in_seconds' => $this->delay,
                ]);
            }
            // dd($numbers);
        }
        $this->success("Agendamento feito com sucesso");
        $this->getTotalDuration();
        $this->next();
    }

    public function boot(
        EvolutionGroupService $evolutionGroupService,
        EvolutionChatService $evolutionChatService
    ) {
        $this->evolutionGroupService = $evolutionGroupService;
        $this->evolutionChatService = $evolutionChatService;
    }

    public function mount(MessageFlow $flow)
    {
        $this->flow = $flow;
        // $this->groups = [];
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
