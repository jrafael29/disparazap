<?php

namespace App\Livewire\Pages\Contact\Verify;

use App\Models\PhonenumberCheck;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public $headers = [
        ['key' => 'id', 'label' => '#'],
        ['key' => 'description', 'label' => 'Descrição'],
        ['key' => 'created_at', 'label' => 'Inicio'],
        ['key' => 'existents', 'label' => 'Existentes'],
        ['key' => 'verified', 'label' => 'Verificados'],
        ['key' => 'count', 'label' => 'Números'],
        ['key' => 'done', 'label' => 'Completo'],
    ];
    public $sub_headers = [
        ['key' => 'id', 'label' => '#'],
    ];
    public array $expanded = [];

    public $showGroups = false;
    // public $groupSelectedId = 0;
    // public $groups;

    public function toggleShowGroups()
    {
        $this->showGroups = !$this->showGroups;
    }

    // public function selectGroup($id)
    // {
    //     if ($id === $this->groupSelectedId) {
    //         $this->reset('groupSelectedId');
    //         return;
    //     }
    //     $this->groupSelectedId = $id;
    // }



    public function addVerifiedPhonenumberCheckToGroup($checkId)
    {
        // dd($checkId);
        // grupo
    }


    public function mount()
    {
        // $this->groups = UserGroup::query()->where('user_id', Auth::user()->id)->get();
    }

    public function render()
    {
        $verifies = PhonenumberCheck::query()
            // ->with(['verifies'])
            ->where('user_id', Auth::user()->id)
            ->orderBy('done', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(7);
        return view('livewire.pages.contact.verify.index', compact('verifies'));
    }
}
