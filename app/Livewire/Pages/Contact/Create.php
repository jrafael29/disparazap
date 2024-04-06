<?php

namespace App\Livewire\Pages\Contact;

use App\Service\UserContactService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Create extends Component
{
    use Toast;

    public $phonenumber;
    public $description;

    private UserContactService $userContactService;

    public function save()
    {
        $this->userContactService->createUserContact(
            userId: Auth::user()->id,
            description: trim($this->description),
            phonenumber: trim($this->phonenumber)
        );
        $this->success("Contato criado com sucesso.");
    }

    public function boot(UserContactService $userContactService)
    {
        $this->userContactService = $userContactService;
    }

    public function render()
    {
        return view('livewire.pages.contact.create');
    }
}
