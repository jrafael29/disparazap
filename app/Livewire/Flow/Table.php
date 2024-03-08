<?php

namespace App\Livewire\Flow;

use App\Models\MessageFlow;
use Livewire\Component;

class Table extends Component
{

    public function messageFlows()
    {
        $flows = MessageFlow::query()->get();
        return $flows;
    }

    public function render()
    {
        return view('livewire.flow.table', [
            'flows' => $this->messageFlows()
        ]);
    }
}
