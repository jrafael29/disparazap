<?php

namespace App\Livewire\Pages\Generate;

use App\Service\PhonenumberService;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Index extends Component
{
    #[Rule('required|gt:9|max:100')]
    public int $count = 100;
    #[Rule('required|gt:10|lt:100')]
    public int $ddd = 81;
    #[Rule('required')]
    public int $ddi = 55;

    public $generatedPhonenumbers = [];

    private PhonenumberService $phonenumberService;

    public function boot(PhonenumberService $phonenumberService)
    {
        $this->phonenumberService = $phonenumberService;
    }

    public function generatePhonenumbers($ddi, $ddd, $count)
    {
        return $this->phonenumberService->generatePhonenumber(
            ddi: $ddi,
            ddd: $ddd,
            amount: $count
        );
    }

    public function save()
    {
        $this->validate();

        $phonenumbers = $this->generatePhonenumbers(
            ddi: $this->ddi,
            ddd: $this->ddd,
            count: $this->count
        );

        $this->generatedPhonenumbers = $phonenumbers;
    }

    public function render()
    {
        return view('livewire.pages.generate.index');
    }
}
