<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Service\UserService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Register extends Component
{
    #[Validate('required|min:4')]
    public ?string $name = '';
    #[Validate('required|email|unique:users')]
    public ?string $email = '';
    #[Validate('required|min:6|confirmed')]
    public ?string $password = '';
    public ?string $password_confirmation = '';

    private UserService $userService;


    public function messages()
    {
        return [
            'email.required' => 'Email obrigatório',
            'email.email' => 'Email inválido',

            'name.required' => 'Nome obrigatório',
            'name.min' => 'O nome precisa ter pelo menos 4 letras',

            'password.required' => 'Senha obrigatória',
            'password.min' => 'A senha precisa ter pelo menos 6 caracteres',
            'password.confirmed' => 'As senhas não correspondem',

        ];
    }

    function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    function createUser($name, $email, $password)
    {
        $user = $this->userService->newUser($name, $email, $password);
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
