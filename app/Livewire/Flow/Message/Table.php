<?php

namespace App\Livewire\Flow\Message;

use App\Models\Message;
use App\Models\MessageFlow;
use Livewire\Attributes\On;
use Livewire\Component;

class Table extends Component
{
    public MessageFlow $flow;

    function handleDeleteMessageClick(Message $message)
    {
        // dd($message);
        $message->delete();
    }

    function reOrderMessages($messageIds, $flowId)
    {
        $messages = Message::query()
            ->where('flow_id', $flowId)
            ->findMany($messageIds)
            ->map(function (Message $message) use ($messageIds) {
                $message->position = array_flip($messageIds)[$message->id];
                return $message->only(['id', 'flow_id', 'type_id', 'text', 'filepath', 'position', 'delay']);
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
        // dd($flow);
        $this->flow = $flow;
    }

    #[On("message::created")]
    public function render()
    {
        return view('livewire.flow.message.table', [
            'messages' => $this->messages()
        ]);
    }
}
