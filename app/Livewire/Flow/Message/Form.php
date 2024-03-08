<?php

namespace App\Livewire\Flow\Message;

use App\Models\MessageFlow;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public string $messageTypeSelected = 'text';
    public string $text = '';
    public int $delay = 2;

    public MessageFlow $flow;


    #[Validate('image|max:5120')] // 5MB Max
    public $image = null;
    #[Validate('file|mimetypes:video/mp4')] // 5MB Max
    public $video;


    function createMediaMessage()
    {
        $userId = Auth::user()->id;
        $extension = '';
        $filename = $userId . '_message_media_' . uniqid() . $extension;
        $flowId = $this->flow->id;

        if ($this->video) {
            $extension = '.' . $this->video->getClientOriginalExtension();
            $filename =  $filename . $extension;
            $filepath = 'videos-upload/' . $filename;
            $this->video->storeAs(path: 'public/videos-upload', name: $filename);
            // Message::query()->insert([
            //     'flow_id' => $flowId,
            //     'text' => $this->text,
            //     'message_type' => 'video',
            //     'filepath' => $filepath,
            //     'delay' => $this->delay
            // ]);
        }
        if ($this->image) {
            $extension = '.' . $this->image->getClientOriginalExtension();
            $filename =  $filename . $extension;
            $filepath = 'images-upload/' . $filename;
            $this->image->storeAs(path: 'public/images-upload', name: $filename);
            // Message::query()->insert([
            //     'flow_id' => $flowId,
            //     'text' => $this->text,
            //     'message_type' => 'image',
            //     'filepath' => $filepath,
            //     'delay' => $this->delay
            // ]);
        }
        $this->reset(['image', 'video', 'text', 'delay']);
        $this->dispatch("message::created");
    }

    function createTextMessage()
    {
        // Message::query()->insert([
        //     'flow_id' => $this->flow->id,
        //     'text' => $this->text,
        //     'message_type' => 'text',
        //     'delay' => $this->delay
        // ]);
        $this->reset(['image', 'video', 'text', 'delay']);
        $this->dispatch("message::created");
    }

    function handleSubmit()
    {
        if ($this->messageTypeSelected !== 'text') {
            $this->createMediaMessage();
            return;
        }
        $this->createTextMessage();
    }

    function changeTypeSelected($type)
    {
        // type must be = 'text' || 'image' || 'video' || 'audio' || 'sticky'
        $this->messageTypeSelected = $type;
    }

    function mount(MessageFlow $flow)
    {
        $this->flow = $flow;
    }

    public function render()
    {
        // $types = MessageType::query()->where('active', 1)->get();
        $types = [
            [
                'name' => "text",
                'description' => "Mensagem de Texto"
            ],
            [
                'name' => "image",
                'description' => "Mensagem de Imagem"
            ],
            [
                'name' => "video",
                'description' => "Mensagem de Video"
            ]
        ];
        return view('livewire.flow.message.form', [
            'types' => $types
        ]);
    }
}
