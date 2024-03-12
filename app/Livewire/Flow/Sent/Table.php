<?php

namespace App\Livewire\Flow\Sent;

use App\Models\FlowToSent;
use App\Models\MessageFlow;
use Livewire\Component;

class Table extends Component
{
    public MessageFlow $flow;
    public function mount(MessageFlow $flow)
    {
        $this->flow = $flow;
    }

    public function formatDate($date): string
    {
        return \Carbon\Carbon::parse($date)->format('H:i d/m/Y');
    }

    public function handleDeleteFlowToSentClick(FlowToSent $flowToSent)
    {
        $flowToSent->delete();
    }

    public function render()
    {
        $flowToSents = FlowToSent::where('flow_id', $this->flow->id)->get();
        return view('livewire.flow.sent.table', [
            'flowToSents' => $flowToSents
        ]);
    }
}
