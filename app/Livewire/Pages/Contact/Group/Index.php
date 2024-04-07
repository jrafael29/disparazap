<?php

namespace App\Livewire\Pages\Contact\Group;

use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    #[Validate('required|min:4')]
    public $name;
    public $description;

    public $groups = [];

    public bool $openModal = false;
    public $expanded = [];
    public $headers = [
        ['key' => 'id', 'label' => '#', 'class' => 'hidden'],
        ['key' => 'identifier', 'label' => '#'],
        ['key' => 'name', 'label' => 'Grupo'],
        ['key' => 'description', 'label' => 'Descrição']
    ];

    public function deleteUserGroup($userGroupId)
    {
        $userGroup = UserGroup::query()->findOrFail($userGroupId);
        $userGroup->delete();
    }

    public function save()
    {
        $this->validate();

        UserGroup::query()->create([
            'user_id' => Auth::user()->id,
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->openModal = false;
    }

    #[On('userGroup::created')]
    public function render()
    {
        $groups = UserGroup::query()
            ->with(['userContacts'])
            ->where('user_id', Auth::user()->id)
            ->get();

        $this->groups = $groups;
        return view('livewire.pages.contact.group.index');
    }
}
