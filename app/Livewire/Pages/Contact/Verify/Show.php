<?php

namespace App\Livewire\Pages\Contact\Verify;

use App\Models\PhonenumberCheck;
use Livewire\Component;

class Show extends Component
{
    public PhonenumberCheck $verify;
    public $showGroups = false;

    public function toggleShowGroups()
    {
        $this->showGroups = !$this->showGroups;
    }

    public function mount(PhonenumberCheck $id)
    {
        $this->verify = $id;
    }
    public function render()
    {
        return view('livewire.pages.contact.verify.show');
    }
}
