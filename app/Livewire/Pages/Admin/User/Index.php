<?php

namespace App\Livewire\Pages\Admin\User;

use App\Models\User;
use Livewire\Component;

class Index extends Component
{


    public $headers = [
        ['key' => 'id', 'label' => '#'],
        ['key' => 'name', 'label' => 'Nome'],
        ['key' => 'email', 'label' => 'Email'],
        // ['key' => 'city.name', 'label' => 'City'],      # <-- nested attributes
        // ['key' => 'fakeColumn', 'label' => 'Fake City'] # <-- this column does not exists
    ];

    public function mount()
    {
    }

    public function render()
    {
        $users = User::all();
        return view('livewire.pages.admin.user.index', [
            'users' => $users
        ]);
    }
}
