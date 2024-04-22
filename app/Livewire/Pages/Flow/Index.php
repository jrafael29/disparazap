<?php

namespace App\Livewire\Pages\Flow;

use Livewire\Component;

class Index extends Component
{
    public $openModal = false;
    public function render()
    {
        return view('livewire.pages.flow.index');
    }
}
