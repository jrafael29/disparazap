<?php

namespace App\Livewire\Instance;

use App\Helpers\Base64ToFile;
use App\Models\Instance;
use App\Models\User;
use App\Repository\InstanceRepository;
use App\Service\InstanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

class Form extends Component
{
    use Toast;
    #[Validate('required')]
    public $description;
    #[Validate('required|min:12|max:13')]
    public $phonenumber;

    private InstanceService $instanceService;

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

    function boot(
        InstanceService $instanceService
    ) {
        $this->instanceService = $instanceService;
    }

    function handleSubmit()
    {
        $this->validate();

        $done = $this->instanceService->createInstance(
            userId: Auth::user()->id,
            description: $this->description,
            phonenumber: $this->phonenumber
        );

        $this->reset(['description', 'phonenumber']);
        if (!$done) {
            $this->error("Ocorreu um erro ao tentar criar a instancia");
        }
        $this->dispatch("instance::created");
        $this->success("Instancia criada com sucesso!");
    }

    public function render()
    {
        return view('livewire.instance.form');
    }
}
