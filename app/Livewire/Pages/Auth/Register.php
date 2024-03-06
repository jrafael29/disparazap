<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Register extends Component
{
    #[Rule('required|min:4')]
    public ?string $name = '';
    #[Rule('required|email|unique:users')]
    public ?string $email = '';
    #[Rule('required|min:6|confirmed')]
    public ?string $password = '';
    #[Rule('required|min:6')]
    public ?string $password_confirmation = '';

    function mount()
    {
    }

    function createUser($name, $email, $password)
    {
        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);

        Auth::login($user);

        $this->redirect(RouteServiceProvider::HOME);
    }

    function handleSubmit()
    {
        $this->validate();
        $this->createUser(
            name: $this->name,
            email: $this->email,
            password: $this->password
        );
    }

    public function render()
    {
        return view('livewire.pages.auth.register')
            ->layout('components.layouts.guest');
    }
}
