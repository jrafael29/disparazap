<?php

namespace App\Livewire\Contact\Verify;

use App\Jobs\AddContactsToGroupJob;
use App\Models\CheckGroup;
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

        $groupsToSkipIds = $this->getGroupsRelatedWithVerify($verifyId);
        if (count($groupsToSkipIds)) {
            $this->groups = UserGroup::query()
                ->where('user_id', Auth::user()->id)
                ->whereNotIn('id', $groupsToSkipIds)
                ->get();
        } else {
            $this->groups = UserGroup::query()
                ->where('user_id', Auth::user()->id)
                ->get();
        }
    }

    public function getGroupsRelatedWithVerify($verifyId)
    {
        return CheckGroup::query()->where('check_id', $verifyId)->get()->pluck('group_id')->toArray();
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
        $user = Auth::user();
        $userGroup = UserGroup::findOrFail($this->groupSelectedId);
        $phonenumbers = $existentPhonenumbers;

        if ($user && $userGroup && !empty($phonenumbers)) {
            // AddContactsToGroupJob::dispatch($userId, $groupId, $phonenumbers)->onQueue('low');
            AddContactsToGroupJob::dispatch($user, $userGroup, $phonenumbers)->onQueue('default');
            CheckGroup::query()->firstOrCreate([
                'check_id' => $check->id,
                'group_id' => $userGroup->id
            ], [
                'check_id' => $check->id,
                'group_id' => $userGroup->id
            ]);
        }
        $this->success("Os contatos existentes serão adicionados ao grupo");
        return $this->redirect('/groups', navigate: true);
    }

    public function render()
    {
        return view('livewire.contact.verify.select-group');
    }
}
