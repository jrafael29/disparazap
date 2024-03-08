<?php

namespace App\Livewire\Flow;

use App\Repository\MessageFlowRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Form extends Component
{
    #[Validate('required|min:4')]
    public ?string $description = '';

    private MessageFlowRepository $messageFlowRepository;
    public function handleSubmit()
    {
        $this->validate();
        $this->messageFlowRepository->createMessageFlow(
            userId: Auth::user()->id,
            description: $this->description
        );
    }

    public function messages()
    {
        return [
            'description.required' => "Campo descrição é obrigatório.",
            'description.min' => "Minimo de 4 letras"
        ];
    }

    public function boot(MessageFlowRepository $messageFlowRepository)
    {
        $this->messageFlowRepository = $messageFlowRepository;
    }

    public function render()
    {
        return view('livewire.flow.form');
    }
}
