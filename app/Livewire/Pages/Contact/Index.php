<?php

namespace App\Livewire\Pages\Contact;

use App\Models\Contact;
use App\Models\UserContact;
use App\Service\Evolution\EvolutionChatService;
use App\Service\UserContactService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;


class Index extends Component
{
    use Toast;
    public $headers = [
        ['key' => 'id', 'label' => '#'],
        ['key' => 'contact.phonenumber', 'label' => 'Telefone'],
        ['key' => 'contact.description', 'label' => 'Descrição'] # <---- nested attributes
    ];
    public $ddds = [
        ['id' => 0, 'name' => "Todos"]
    ];
    public $contacts = [];
    public $dddSelected;
    public $selectedContacts = [];
    public $countSelectedContacts = 0;
    public bool $openModal = false;
    public $description;
    #[Validate('required')]
    public $phonenumber;
    public bool $isValidPhonenumber = false;

    private EvolutionChatService $evolutionChatService;
    private UserContactService $userContactService;

    public function boot(
        EvolutionChatService $evolutionChatService,
        UserContactService $userContactService
    ) {
        $this->evolutionChatService = $evolutionChatService;
        $this->userContactService = $userContactService;
    }

    public function handleSubmit()
    {
        $this->validate();
        $this->userContactService->createUserContact(
            userId: Auth::user()->id,
            description: trim($this->description),
            phonenumber: trim($this->phonenumber)
        );
        $this->success("Contato criado com sucesso.");
        $this->reset(['description', 'phonenumber', 'isValidPhonenumber']);
    }

    public function validatePhonenumber()
    {
        $this->validate();
        // pega alguma instancia do usuario;
        $result = $this->evolutionChatService->checkNumbersExistence(
            instanceName: '2-instance-1',
            numbers: [$this->phonenumber]
        );
        if (reset($result)) {
            $this->isValidPhonenumber = true;
        } else {
            $this->isValidPhonenumber = false;
            $this->warning(
                title: "Telefone não existe no whatsapp, portanto não será salvo.",
                timeout: 10000
            );
        }
    }

    public function updateSelectedContacts()
    {
        $this->countSelectedContacts = count($this->selectedContacts);
    }

    public function orderContacts()
    {
        if ($this->dddSelected == 0) {
            $this->contacts = UserContact::query()->with(['contact'])
                ->where('user_id', Auth::user()->id)
                ->whereHas('contact', function ($q) {
                    $q->where('active', 1);
                })
                ->get();
        } else {
            $this->contacts = UserContact::query()->with(['contact'])
                ->where('user_id', Auth::user()->id)
                ->whereHas('contact', function ($q) {
                    $q->where('phonenumber', 'like', "55{$this->dddSelected}%")
                        ->where('active', 1);
                })
                ->get();
        }
        $this->render();
    }

    public function deleteSelectedContacts()
    {
        foreach ($this->selectedContacts as $userContactId) {
            UserContact::query()->findOrFail($userContactId)?->delete();
        }
        $this->redirect('/contacts');
    }

    public function delete(UserContact $uc)
    {
        $uc->delete();
    }

    public function mount()
    {
        for ($i = 11; $i < 100; $i++) {
            array_push($this->ddds, ['id' => $i, 'name' => $i,]);
        }
    }


    public function render()
    {
        $this->contacts = UserContact::with(['contact'])
            ->where('user_id', Auth::user()->id)
            ->whereHas('contact', function ($q) {
                $q->where('active', 1);
            })
            ->get();
        // dd($this->contacts);
        return view('livewire.pages.contact.index');
    }
}
