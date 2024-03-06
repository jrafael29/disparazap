<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Component;

class Login extends Component
{
    public function render()
    {
        return view('livewire.pages.auth.login')
            ->layout('components.layouts.guest');
    }
}
