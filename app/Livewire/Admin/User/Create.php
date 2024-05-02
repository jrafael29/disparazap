<?php

namespace App\Livewire\Admin\User;

use App\Service\AuthService;
use App\Service\UserService;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

class Create extends Component
{
    use Toast;
    #[Validate('min:4')]
    public $name = '';

    #[Validate('required|email|unique:users')]
    public $email = '';

    #[Validate('required|min:6')]
    public string $password = '';

    public string $password_confirmation = '';
    public $openModal = false;
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

        ];
    }


    function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    function createUser($name, $email, $password)
    {
        $result = $this->userService->newUser($name, $email, $password);
        if ($result) {
            $this->dispatch("user::created");
            $this->reset('name', 'email', 'password');
            $this->openModal = false;
            $this->success("Usuário criado com sucesso");
            return true;
        }
        return false;
    }

    public function save()
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
        return view('livewire.admin.user.create');
    }
}
