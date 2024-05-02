<?php

namespace App\Livewire\Pages\Admin\User;

use App\Models\User;
use Livewire\Component;

class Show extends Component
{
    public $user;
    public function mount(User $id)
    {
        $this->user = $id;
    }

    public function render()
    {
        return view('livewire.pages.admin.user.show');
    }
}
