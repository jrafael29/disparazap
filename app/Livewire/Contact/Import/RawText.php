<?php

namespace App\Livewire\Contact\Import;

use App\Helpers\Phonenumber;
use App\Models\Instance;
use App\Models\UserGroup;
use App\Service\Evolution\EvolutionChatService;
use App\Service\UserContactService;
use App\Service\UserGroupService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

class RawText extends Component
{
    use Toast;
    public $rawText;
    public bool $includeDdd = false;
    #[Validate('integer|max:99|min:10')]
    public $ddd = 86;

    public bool $includeDdi = false;
    #[Validate('integer|max:2000|min:1')]
    public $ddi = 55;

    public $phonenumbers = [];

    public $existentPhonenumbers = [];
    public $inexistentPhonenumbers = [];


    public $countAllPhonenumbers = 0;
    public $countExistentPhonenumbers = 0;
    public $countInexistentPhonenumbers = 0;

    public $show = false;

    public $openModal = false;


    public $userOnlineInstancesCount = 0;

    public $steps = 4; // quantidade de passos no
    public $step = 1; // step atual

    public $groups = [];
    public $groupSelectedId = 0;

    private EvolutionChatService $evolutionChatService;
    private UserContactService $userContactService;
    private UserGroupService $userGroupService;

    public function selectGroup($id)
    {
        $this->groupSelectedId = $id;
    }

    public function toggleCollapseForm()
    {
        $this->show = !$this->show;
    }

    public function toggleIncludeDdd()
    {
        if ($this->includeDdd) {
            $this->includeDdi = false;
        }
        $this->includeDdd = !$this->includeDdd;
    }
    public function toggleIncludeDdi()
    {
        if (!$this->includeDdd) {
            return $this->error("Para adicionar o DDI, você precisa também adicionar o DDD");
        }
        $this->includeDdi = !$this->includeDdi;
    }

    public function saveExistentPhonenumbers()
    {
        $this->userContactService->storeManyUserContacts(
            userId: Auth::user()->id,
            phonenumbers: $this->existentPhonenumbers
        );

        $this->next();
        $this->success("Contatos salvos com sucesso");
    }

    public function addContactsToGroup()
    {

        $this->userGroupService->addContactsToGroup(
            userId: Auth::user()->id,
            groupId: $this->groupSelectedId,
            contacts: $this->existentPhonenumbers
        );

        $this->reset('rawText', 'existentPhonenumbers', 'inexistentPhonenumbers');

        $this->redirect('/contacts/groups');
    }

    public function checkExistence()
    {
        $firstInstanceName = Instance::query()->where('user_id', Auth::user()->id)->first()?->name;
        if (!$firstInstanceName) return false;
        $numbersExistence = $this->evolutionChatService->checkNumbersExistence(
            numbers: $this->phonenumbers,
            instanceName: $firstInstanceName
        );


        $phonenumbersExistenceSeparated = Phonenumber::divideNumberExistence($numbersExistence);
        // dd($phonenumbersExistenceSeparated);
        // $this->reset('existentPhonenumbers', 'inexistentPhonenumbers');
        $this->existentPhonenumbers = [];
        $this->inexistentPhonenumbers = [];
        $this->existentPhonenumbers = $phonenumbersExistenceSeparated['existents'];
        $this->inexistentPhonenumbers = $phonenumbersExistenceSeparated['inexistents'];

        $this->next();
    }

    public function next()
    {
        if ($this->step === $this->steps) return false;
        $this->step++;
    }
    public function prev()
    {
        if ($this->step == 1) return false;
        $this->step--;
    }

    public function handleSubmit()
    {

        if (strlen($this->rawText) < 5) return false;

        $phonenumbers = Phonenumber::getPhonenumbersFromText(
            text: $this->rawText,
            allowRepeated: false
        );
        if ($this->includeDdd) {
            $phonenumbers = Phonenumber::addDddToPhonenumbers(
                phonenumbers: $phonenumbers,
                ddd: $this->ddd
            );
        }
        if ($this->includeDdi) {
            $phonenumbers = Phonenumber::addDdiToPhonenumbers(
                phonenumbers: $phonenumbers,
                ddi: $this->ddi
            );
        }
        $this->phonenumbers = $phonenumbers;

        $this->next();
    }

    public function mount()
    {
        // user online instances count
        $userId = Auth::user()->id;
        $userInstancesCount = Instance::query()->where('user_id', $userId)->where('online', 1)->count();
        $this->userOnlineInstancesCount = $userInstancesCount;
    }

    public function boot(
        EvolutionChatService $evolutionChatService,
        UserContactService $userContactService,
        UserGroupService $userGroupService
    ) {
        $this->evolutionChatService = $evolutionChatService;
        $this->userContactService = $userContactService;
        $this->userGroupService = $userGroupService;
    }

    #[On('userGroup::created')]
    public function render()
    {
        $this->groups = UserGroup::query()->where('user_id', Auth::user()->id)->get();
        return view('livewire.contact.import.raw-text');
    }
}
