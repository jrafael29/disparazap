<?php

namespace App\Livewire\Flow\Message;

use App\Models\Message;
use App\Models\MessageFlow;
use Livewire\Component;

class Table extends Component
{
    private MessageFlow $flow;
    function reOrderMessages($messageIds)
    {
        $messages = Message::query()
            ->where('flow_id', $this->flow->id)
            ->findMany($messageIds)
            ->map(function (Message $message) use ($messageIds) {
                $message->position = array_flip($messageIds)[$message->id];
                return $message;
            });
        Message::query()->upsert($messages->toArray(), ['id'], ['position']);
    }

    public function messages()
    {
        $messages = Message::query()
            ->where('flow_id', $this->flow->id)
            ->orderBy('position', 'asc')
            ->get();
        return $messages;
    }

    public function mount(MessageFlow $flow)
    {
        $this->flow = $flow;
    }

    public function render()
    {
        return view('livewire.flow.message.table', [
            'messages' => $this->messages()
        ]);
    }
}
