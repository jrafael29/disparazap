<?php

namespace App\Livewire\Pages\Admin\User;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{


    public $headers = [
        ['key' => 'id', 'label' => '#'],
        ['key' => 'name', 'label' => 'Nome'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'wallet.credit', 'label' => 'Créditos'],      # <-- nested attributes
        // ['key' => 'fakeColumn', 'label' => 'Fake City'] # <-- this column does not exists
    ];

    public function mount()
    {
    }

    #[On('user::created')]
    public function render()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('livewire.pages.admin.user.index', [
            'users' => $users
        ]);
    }
}
