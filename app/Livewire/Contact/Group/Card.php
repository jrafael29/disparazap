<?php

namespace App\Livewire\Contact\Group;

use App\Exports\GroupUserContactsExport;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Maatwebsite\Excel\Exporter;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

class Card extends Component
{
    use Toast;
    #[Validate('required|min:4|max:100')]
    public $name = '';
    #[Validate('required|min:4|max:255')]
    public $description = '';

    public $modalEdit = false;
    public $modalExport = false;
    public $modalDelete = false;

    public UserGroup $group;

    private Exporter $exporter;

    public function deleteUserGroup()
    {
        $this->userGroup->delete();
        $this->redirect('/groups', navigate: true);
    }

    public function handleEditSubmit()
    {
        $this->validate();

        $this->group->update([
            'name' => $this->name,
            'description' => $this->description
        ]);
        $this->redirect('/groups', navigate: true);
    }

    public function export()
    {
        $fileName =  uniqid(Str::slug(Str::lower(Auth::user()->name . ' contacts')) . '-') . '.csv';
        $this->modalExport = false;
        $this->success("Contatos baixado com sucesso.");
        return Excel::download(new GroupUserContactsExport($this->group), $fileName, \Maatwebsite\Excel\Excel::CSV);
    }

    public function boot(Exporter $exporter)
    {
        $this->exporter = $exporter;
    }

    public function mount(UserGroup $group)
    {
        $this->group = $group;

        $this->name = $this->group->name;
        $this->description = $this->group->description;
    }
    public function render()
    {
        return view('livewire.contact.group.card');
    }
}
