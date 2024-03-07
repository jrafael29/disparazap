<?php

namespace App\Livewire\Instance;

use App\Helpers\Base64ToFile;
use App\Models\Instance;
use App\Models\User;
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

    function messages()
    {
        return [
            'description.required' => "A descrição é obrigatória.",

            'phonenumber.required' => "O número é obrigatório.",
            'phonenumber.min' => "O número precisa ter no minimo 12 caracteres",
            'phonenumber.max' => "O número precisa ter no maximo 13 caracteres",
        ];
    }

    function createInstanceRepository($userId, $description, $phonenumber): Instance
    {
        if (empty($userId) || empty($description) || empty($phonenumber)) return false;

        $instanceName = $userId . '-instance-';

        $instance = Instance::query()->create([
            'user_id' => $userId,
            'name' => $instanceName,
            'description' => $description,
            'phonenumber' => $phonenumber
        ]);

        $instance->name = $instance->name . $instance->id;
        $instance->save();
        return $instance;
    }

    function createEvolutionInstanceService($instanceName, $phonenumber)
    {
        $apiUrl = 'https://evolutionbot.joserafael.dev.br';
        $createInstanceRoute = '/instance/create';
        $url = $apiUrl . $createInstanceRoute;
        $body = [
            'instanceName' => $instanceName,
            'number' => $phonenumber,
            'qrcode' => true
        ];

        $headers = [
            'apiKey' => '0417bf43b0a8969bd6685bcb49d783d'
        ];

        $response = Http::withHeaders($headers)
            ->post($url, $body);

        $data = $response->body();

        $instance = $response->json('instance');
        if (!$instance) {
            return false;
        }
        $instanceApiKey = $response->json('hash')['apikey'];
        $qr = $response->json('qrcode');
        $instanceData = [
            'apikey' => $instanceApiKey,
            'instance' => $instance,
            'base64' => $qr['base64']
        ];
        return $instanceData;
    }



    function createInstance($userId, $description, $phonenumber)
    {
        $instanceModel = $this->createInstanceRepository(
            userId: $userId,
            description: $description,
            phonenumber: $phonenumber
        );
        $evolutionInstanceData = $this->createEvolutionInstanceService(
            instanceName: $instanceModel->name,
            phonenumber: $instanceModel->phonenumber
        );

        if (!empty($evolutionInstanceData['base64'])) {
            $filename = Base64ToFile::storeImageFromBase64($evolutionInstanceData['base64'], 'qrcodes/qr_' . uniqid() . '.png');
            if ($filename) {
                $instanceModel->qrcode_path = $filename;
                $instanceModel->save();

                return true;
            }
        }
        return false;
    }

    function mount()
    {
    }

    function handleSubmit()
    {
        $this->validate();
        $done = $this->createInstance(
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
