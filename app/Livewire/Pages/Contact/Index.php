<?php

namespace App\Livewire\Pages\Contact;

use App\Models\Contact;
use App\Models\UserContact;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $headers = [
        ['key' => 'contact.id', 'label' => '#'],
        ['key' => 'contact.phonenumber', 'label' => 'Telefone'],
        ['key' => 'contact.description', 'label' => 'Descrição'] # <---- nested attributes
    ];
    public $ddds = [
        ['id' => 0, 'name' => "Todos"]
    ];
    public $contacts = [];

    public function teste($id)
    {
        if ($id === 0) {
            // $this->contacts = Contact::query()
            //     ->where('phonenumber', 'like', "55{}")
            //     ->where('user_i')
        }
        // $this->contacts = Contact::query()->where('phonenumber', 'like', "55{}")
        // dd("!teste");
    }

    public function mount()
    {
        for ($i = 11; $i < 100; $i++) {
            array_push($this->ddds, ['id' => $i, 'name' => $i,]);
        }
    }

    public function render()
    {
        $this->contacts = UserContact::with(['contact'])
            ->whereHas('contact', function ($q) {
                $q->where('active', 1);
            })
            ->where('user_id', Auth::user()->id)
            ->get();
        return view('livewire.pages.contact.index');
    }
}
