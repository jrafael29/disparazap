<?php

namespace App\Livewire\Contact\Verify;

use App\Jobs\AddContactsToGroupJob;
use App\Models\PhonenumberCheck;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class SelectGroup extends Component
{
    use Toast;
    public $showGroups = false;
    public $groupSelectedId = 0;
    public $groups;

    public $checkId;

    public function mount($showGroups, $verifyId)
    {
        $this->checkId = $verifyId;
        $this->showGroups = $showGroups;
        $this->groups = UserGroup::query()->where('user_id', Auth::user()->id)->get();
    }
    public function toggleShowGroups()
    {
        $this->showGroups = !$this->showGroups;
    }

    public function selectGroup($id)
    {
        if ($id === $this->groupSelectedId) {
            $this->reset('groupSelectedId');
            return;
        }
        $this->groupSelectedId = $id;
    }

    public function addVerifiedPhonenumberCheckToGroup()
    {
        $check = PhonenumberCheck::query()->with(['verifies'])->findOrFail($this->checkId);
        $existentPhonenumbers = $check->verifies()
            ->where('verified', 1)
            ->where('isOnWhatsapp', 1)
            ->get(['phonenumber'])
            ->pluck('phonenumber')
            ->toArray();
        $userId = Auth::user()->id;
        $groupId = $this->groupSelectedId;
        $phonenumbers = $existentPhonenumbers;
        AddContactsToGroupJob::dispatch($userId, $groupId, $phonenumbers)->onQueue('low');

        $this->success("Os contatos serão adicionados ao grupo");
        return $this->redirect('/groups', navigate: true);
    }

    public function render()
    {
        return view('livewire.contact.verify.select-group');
    }
}
