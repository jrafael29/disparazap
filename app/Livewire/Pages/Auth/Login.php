<?php

namespace App\Livewire\Pages\Auth;

use App\Providers\AuthServiceProvider;
use App\Providers\RouteServiceProvider;
use App\Service\AuthService;
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

    private AuthService $authService;

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
        $result = $this->authService->login($email, $password);
        if ($result['success'] === false) {
            $this->reset(['password']);
            $this->addError('email', $result['message']);
            return;
        }
        return redirect()->to(RouteServiceProvider::HOME);
    }

    function handleSubmit()
    {
        $this->validate();
        $this->attemptLogin($this->email, $this->password);
    }

    function boot(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function render()
    {
        return view('livewire.pages.auth.login')
            ->layout('components.layouts.guest');
    }
}
