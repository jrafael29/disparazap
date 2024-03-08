<?php

namespace App\Livewire\Pages\Flow\Message;

use App\Models\MessageFlow;
use Livewire\Component;

class Index extends Component
{
    public MessageFlow $flow;
    public function mount(MessageFlow $flow)
    {
        $this->flow = $flow;
    }

    public function render()
    {
        return view('livewire.pages.flow.message.index');
    }
}
