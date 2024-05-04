<?php

namespace App\Livewire\Pages\Contact\Verify;

use App\Models\PhonenumberCheck;
use Livewire\Component;

class Show extends Component
{
    public PhonenumberCheck $verify;
    public $showGroups = false;
    public $existentPhonenumbers = [];
    public $inexistentPhonenumbers = [];

    public function toggleShowGroups()
    {
        $this->showGroups = !$this->showGroups;
    }

    public function mount(PhonenumberCheck $id)
    {
        $this->verify = $id;
        $this->existentPhonenumbers = $id->verifies()->where('isOnWhatsapp', 1)->pluck('phonenumber');
        $this->inexistentPhonenumbers = $id->verifies()->where('isOnWhatsapp', 0)->pluck('phonenumber');
    }
    public function render()
    {
        return view('livewire.pages.contact.verify.show');
    }
}
