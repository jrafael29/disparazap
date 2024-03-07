<?php

namespace App\Livewire\Pages\Auth;

use App\Providers\AuthServiceProvider;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Validate('required|email')]
    public ?string $email = '';
    #[Validate('required')]
    public ?string $password = '';
    public bool $remember = false;

    public function messages()
    {
        return [
            'email.required' => 'Email obrigatório',
            'email.email' => 'Email inválido',

            'password.required' => 'Senha obrigatória',

        ];
    }

    function attemptLogin($email, $password)
    {
        $credentials = [
            'email' => $email,
            'password' => $password
        ];
        if (Auth::attempt($credentials)) {
            return redirect()->to(RouteServiceProvider::HOME);
        }

        $this->reset(['password']);
        $this->addError('email', 'Email e/ou senha inválidos');
    }

    function handleSubmit()
    {
        $this->validate();
        $this->attemptLogin($this->email, $this->password);
    }

    public function render()
    {
        return view('livewire.pages.auth.login')
            ->layout('components.layouts.guest');
    }
}
