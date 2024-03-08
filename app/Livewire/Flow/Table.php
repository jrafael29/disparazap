<?php

namespace App\Livewire\Flow;

use App\Models\MessageFlow;
use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class Table extends Component
{
    use Toast;
    public function deleteFlowClick(MessageFlow $flow)
    {
        $flow->delete();
        $this->success("Fluxo excluido com sucesso.");
    }

    public function messageFlows()
    {
        $flows = MessageFlow::query()->get();
        return $flows;
    }

    #[On('flow::created')]
    public function render()
    {
        return view('livewire.flow.table', [
            'flows' => $this->messageFlows()
        ]);
    }
}
