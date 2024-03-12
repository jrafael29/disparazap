<?php

namespace App\Livewire\Pages\Flow\Sent;

use App\Models\Instance;
use App\Models\MessageFlow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public MessageFlow $flow;
    function mount(MessageFlow $flow)
    {
        $this->flow = $flow;

        // quantidade de instancias online do usuario.

    }

    public function render()
    {
        $onlineInstances = Instance::query()->where('user_id', Auth::user()->id)->where('online', 1)->count();
        return view('livewire.pages.flow.sent.index', [
            'onlineInstances' => $onlineInstances
        ]);
    }
}
