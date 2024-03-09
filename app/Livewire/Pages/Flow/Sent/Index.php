<?php

namespace App\Livewire\Pages\Flow\Sent;

use App\Models\MessageFlow;
use Livewire\Component;

class Index extends Component
{
    public MessageFlow $flow;
    function mount(MessageFlow $flow)
    {
        $this->flow = $flow;
    }

    public function render()
    {
        return view('livewire.pages.flow.sent.index');
    }
}
