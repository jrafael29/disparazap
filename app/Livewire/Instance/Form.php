<?php

namespace App\Livewire\Instance;

use Livewire\Attributes\Validate;
use Livewire\Component;

class Form extends Component
{
    #[Validate('required')]
    public $description;
    #[Validate('required|min:12|max:13')]
    public $phonenumber;

    function messages()
    {
        return [
            'description.required' => "A descrição é obrigatória.",

            'phonenumber.required' => "O número é obrigatório.",
            'phonenumber.min' => "O número precisa ter no minimo 12 caracteres",
            'phonenumber.max' => "O número precisa ter no maximo 13 caracteres",
        ];
    }

    function mount()
    {
    }

    function handleSubmit()
    {
        $this->validate();
    }

    public function render()
    {
        return view('livewire.instance.form');
    }
}
