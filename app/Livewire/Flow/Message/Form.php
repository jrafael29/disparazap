<?php

namespace App\Livewire\Flow\Message;

use App\Models\Message;
use App\Models\MessageFlow;
use App\Models\MessageType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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


    function createMediaMessage($flowId, $text, $typeId, $delay)
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
            Message::query()->create([
                'flow_id' => $flowId,
                'text' => $text,
                'type_id' => $typeId,
                'filepath' => $filepath,
                'delay' => $delay
            ]);
        }
        if ($this->image) {
            $extension = '.' . $this->image->getClientOriginalExtension();
            $filename =  $filename . $extension;
            $filepath = 'images-upload/' . $filename;
            $this->image->storeAs(path: 'public/images-upload', name: $filename);
            Message::query()->create([
                'flow_id' => $flowId,
                'text' => $text,
                'type_id' => $typeId,
                'filepath' => $filepath,
                'delay' => $delay
            ]);
        }
        $this->reset(['image', 'video', 'text', 'delay']);
        $this->dispatch("message::created");
    }

    function createTextMessage($flowId, $text, $typeId, $delay)
    {
        $flow = MessageFlow::query()->find($flowId);

        Message::query()->create([
            'flow_id' => $flow->id,
            'type_id' => $typeId,
            'text' => $text,
            'delay' => $delay
        ]);
        return true;
    }

    function handleSubmit()
    {
        $typeId = MessageType::query()->where('name', $this->messageTypeSelected)->first()?->id;
        if (!$typeId) return false;
        if ($this->messageTypeSelected !== 'text') {
            $this->createMediaMessage(
                flowId: $this->flow->id,
                text: $this->text,
                typeId: $typeId,
                delay: $this->delay
            );
            return;
        }
        $this->createTextMessage(
            flowId: $this->flow->id,
            text: $this->text,
            typeId: $typeId,
            delay: $this->delay
        );
        $this->reset(['image', 'video', 'text', 'delay']);
        $this->dispatch("message::created");
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
        $types = MessageType::query()->where('active', 1)->get();
        return view('livewire.flow.message.form', [
            'types' => $types
        ]);
    }
}
