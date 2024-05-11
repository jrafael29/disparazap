<?php

namespace App\Livewire\Pages\Contact\Group;

use App\Exports\GroupUserContactsExport;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Maatwebsite\Excel\Exporter;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

class Index extends Component
{
    use Toast;

    public $groups = [];

    public bool $openModal = false;
    public $expanded = [];
    public $headers = [
        ['key' => 'id', 'label' => '#', 'class' => 'hidden'],
        ['key' => 'identifier', 'label' => '#'],
        ['key' => 'name', 'label' => 'Grupo'],
        ['key' => 'contactsCount', 'label' => 'Contatos'],
        ['key' => 'description', 'label' => 'Descrição']
    ];


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
            ->withCount('userContacts')
            ->where('user_id', Auth::user()->id)
            ->orderBy('user_contacts_count', 'desc')
            ->get();

        $this->groups = $groups;
        return view('livewire.pages.contact.group.index');
    }
}
