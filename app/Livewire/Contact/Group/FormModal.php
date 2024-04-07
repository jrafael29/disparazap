<?php

namespace App\Livewire\Contact\Group;

use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

class FormModal extends Component
{
    use Toast;
    #[Validate('required|min:4')]
    public $name;
    public $description;

    public $openModal = false;

    public function save()
    {
        $this->validate();

        UserGroup::query()->create([
            'user_id' => Auth::user()->id,
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->dispatch("userGroup::created");
        $this->success(
            title: "Grupo criado com sucesso.",
            timeout: 5000
        );
        $this->openModal = false;
    }
    public function render()
    {
        return view('livewire.contact.group.form-modal');
    }
}
