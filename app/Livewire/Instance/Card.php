<?php

namespace App\Livewire\Instance;

use App\Models\Instance;
use Livewire\Component;

class Card extends Component
{
    function mount(Instance $instance)
    {
        dd($instance);
    }
    public function render()
    {
        return view('livewire.instance.card');
    }
}
