<?php

namespace App\Livewire\Flow\Sent;

use App\Helpers\Phonenumber as PhonenumberHelper;
use App\Models\Contact;
use App\Models\FlowToSent;
use App\Models\Instance;
use App\Models\MessageFlow;
use App\Models\UserContact;
use App\Service\Evolution\EvolutionChatService;
use App\Service\Evolution\EvolutionGroupService;
use App\Service\FlowToSentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
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
    public $phonenumbersExistence = [];

    public $hours = '';
    public $minutes = '';
    public $seconds = '';

    public $countAllPhonenumbers = 0;
    public $countExistentPhonenumbers = 0;
    public $countInexistentPhonenumbers = 0;

    // for dev
    public $allowRepeatTarget = true;

    private EvolutionGroupService $evolutionGroupService;
    private EvolutionChatService $evolutionChatService;
    private FlowToSentService $flowToSentService;

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
                    $phonenumber = PhonenumberHelper::getPhonenumberFromParticipant($participant, $ddi);
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
                $phonenumbers = PhonenumberHelper::getPhonenumbersFromText(
                    text: $this->rawText,
                    allowRepeated: $this->allowRepeatTarget
                );

                if ($this->allowRepeatTarget) {
                    $this->phonenumbers = $phonenumbers;
                    return true;
                }
                $firstInstanceName = Instance::find($this->selectedInstances[0])?->name;
                $numbersExistence = $this->evolutionChatService->checkNumbersExistence(
                    numbers: $phonenumbers,
                    instanceName: $firstInstanceName
                );
                foreach ($numbersExistence as $key => $value) {
                    $this->countAllPhonenumbers++;
                    $value ? $this->countExistentPhonenumbers++ : $this->countInexistentPhonenumbers++;
                }
                // dd($numbersExistence);
                $this->phonenumbersExistence = $numbersExistence;
                $this->phonenumbers = array_keys($numbersExistence);
                return true;

                break;
            case 'group-contacts':
                $groupsPhonenumber = $this->getGroupsParticipantsPhonenumber(
                    groups: $this->groupsSelected,
                    ddi: 55
                );
                $this->groupsParticipantsPhonenumber = array_values($groupsPhonenumber);
                $result = PhonenumberHelper::getPhonenumbersFromGroupsParticipants($this->groupsParticipantsPhonenumber);
                $this->phonenumbers = $result;
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

        $numbers = $this->phonenumbers;
        // dd($numbers);
        $instances = $this->selectedInstances;
        $allInstancesPhonenumbers = PhonenumberHelper::dividePhonenumbersByInstances(
            instances: $instances,
            phonenumbers: $numbers,
        );

        foreach ($allInstancesPhonenumbers as $instanceId => $phonenumbers) {
            foreach ($phonenumbers as $index => $phonenumber) {
                $toSentDate = Carbon::parse($this->toSendDate)->addSeconds(($this->delay * $index) + 5);
                // dd($sendDate);
                $this->flowToSentService->createFlowToSent(
                    userId: Auth::user()->id,
                    flowId: $this->flow->id,
                    phonenumber: $phonenumber,
                    instanceId: $instanceId,
                    sendAt: $toSentDate,
                    delayInSeconds: $this->delay
                );
            }
        }
        $this->success("Agendamento feito com sucesso");
        $this->getTotalDuration();
        $this->next();
    }

    public function boot(
        EvolutionGroupService $evolutionGroupService,
        EvolutionChatService $evolutionChatService,
        FlowToSentService $flowToSentService
    ) {
        $this->evolutionGroupService = $evolutionGroupService;
        $this->evolutionChatService = $evolutionChatService;
        $this->flowToSentService = $flowToSentService;
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

        $this->allowRepeatTarget = env("ALLOW_REPEAT_TARGET_FOR_DEV") ?? false;

        return view('livewire.flow.sent.steps', [
            'instances' => $instances
        ]);
    }
}
