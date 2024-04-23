<?php

namespace App\Livewire\Contact\Import;

use App\Helpers\Phonenumber;
use App\Jobs\AddContactsToGroupJob;
use App\Jobs\StoreContactsJob;
use App\Models\Instance;
use App\Models\User;
use App\Models\UserGroup;
use App\Service\Evolution\EvolutionGroupService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class GroupContacts extends Component
{
    private int $max_groups_selected_allowed = 3;
    // if want update the value, must be updated both values
    // for security because this props is visible in frontend;
    public int $public_max_groups_selected_allowed = 3;

    public $steps = 4; // quantidade de passos no
    public $step = 1; // step atual


    public $instances = []; // instancias do usuario

    public $instancesGroups = []; // grupos de todas as instancias do usuario.

    public $selectedInstances = []; // todas as instancias selecionadas.

    public $groupsSelected; // grupos do whatsapp selecionados 

    // grupo selecionado pelo usuario para adicionar os numeros
    public $disparaGroupSelectedId = 0;

    // like be '[3217321@jid.wpp => ["192292992", "20202000"], ...]'
    public $groupsParticipantsPhonenumber = []; // numero dos participantes dos grupos selecionados

    // public $hours = '';
    // public $minutes = '';
    // public $seconds = '';

    public $phonenumbers = [];

    private EvolutionGroupService $evolutionGroupService;
    public function next()
    {
        if ($this->step === $this->steps) return false;
        $this->step++;
        if ($this->step === 2) $this->getGroupsParticipants();
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

    function selectDisparaGroup($id)
    {
        if ($id === $this->disparaGroupSelectedId) {
            $this->reset('disparaGroupSelectedId');
            return;
        }
        $this->disparaGroupSelectedId = $id;
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
                    $phonenumber = Phonenumber::getPhonenumberFromParticipant($participant, $ddi);
                    if ($phonenumber)
                        $numbers[] = $phonenumber;
                }
                $phoneNumbers[$groupId] =  $numbers;
            }
            $groupsParticipantsNumber[] = $phoneNumbers;
        }
        return $groupsParticipantsNumber;
    }

    function getGroupsParticipants()
    {
        $groupsPhonenumber = $this->getGroupsParticipantsPhonenumber(
            groups: $this->groupsSelected,
            ddi: 55
        );
        $this->groupsParticipantsPhonenumber = array_values($groupsPhonenumber);
        $allParticipantsPhonenumber = Phonenumber::getPhonenumbersFromGroupsParticipants($this->groupsParticipantsPhonenumber);
        $this->phonenumbers = $allParticipantsPhonenumber;
    }

    public function getInstancesGroups()
    {
        $fullData = [];
        foreach ($this->instances as $key => $instance) {
            $fullData[$instance->id] = $this->evolutionGroupService->getGroups($instance->name);
        }
        $this->instancesGroups = $fullData;
    }

    private function loadOnlineInstances()
    {
        // $this->instance = Auth::user()->instances->where('online', 1)->first();
        $this->instances = Instance::query()->where('user_id', Auth::user()->id)->where('online', 1)->get();
    }

    public function handleSubmit()
    {
        $phonenumbers = $this->phonenumbers;
        $groupId = $this->disparaGroupSelectedId;
        $userId = Auth::user()->id;
        if (empty($this->phonenumbers) || !$groupId) return;
        StoreContactsJob::dispatch($userId, $phonenumbers);
        // disparar job para adicionar contatos a um grupo
        AddContactsToGroupJob::dispatch($userId, $groupId, $phonenumbers);

        $this->redirect('/groups', true);
    }


    public function mount()
    {
        $this->groupsSelected = collect();
        $this->loadOnlineInstances();
        $this->getInstancesGroups();
    }

    public function boot(EvolutionGroupService $evolutionGroupService)
    {
        $this->evolutionGroupService = $evolutionGroupService;
    }

    #[On('userGroup::created')]
    public function render()
    {
        $disparaGroups = UserGroup::query()->where('user_id', Auth::user()->id)->get();
        return view('livewire.contact.import.group-contacts', compact('disparaGroups'));
    }
}
