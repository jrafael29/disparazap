<?php

namespace App\Livewire\Pages\Auth;

use App\Providers\AuthServiceProvider;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public ?string $email = '';
    public ?string $password = '';
    public bool $remember = false;

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
        $this->addError('email', 'The provided credentials do not match our records.');
    }

    function handleSubmit()
    {
        $this->attemptLogin($this->email, $this->password);
    }

    public function render()
    {
        return view('livewire.pages.auth.login')
            ->layout('components.layouts.guest');
    }
}
