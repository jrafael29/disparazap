<?php

namespace App\Livewire\Pages\Contact;

use App\Models\Contact;
use App\Models\UserContact;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $headers = [
        ['key' => 'id', 'label' => '#'],
        ['key' => 'contact.phonenumber', 'label' => 'Telefone'],
        ['key' => 'contact.description', 'label' => 'Descrição'] # <---- nested attributes
    ];
    public $ddds = [
        ['id' => 0, 'name' => "Todos"]
    ];
    public $contacts = [];
    public $dddSelected;
    public $selectedContacts = [];
    public $countSelectedContacts = 0;

    public function updateSelectedContacts()
    {
        $this->countSelectedContacts = count($this->selectedContacts);
    }

    public function orderContacts()
    {
        if ($this->dddSelected == 0) {
            $this->contacts = UserContact::query()->with(['contact'])
                ->where('user_id', Auth::user()->id)
                ->whereHas('contact', function ($q) {
                    $q->where('active', 1);
                })
                ->get();
        } else {
            $this->contacts = UserContact::query()->with(['contact'])
                ->where('user_id', Auth::user()->id)
                ->whereHas('contact', function ($q) {
                    $q->where('phonenumber', 'like', "55{$this->dddSelected}%")
                        ->where('active', 1);
                })
                ->get();
        }
        $this->render();
    }

    public function deleteSelectedContacts()
    {
        foreach ($this->selectedContacts as $userContactId) {
            UserContact::query()->findOrFail($userContactId)?->delete();
        }
        $this->redirect('/contacts');
    }

    public function delete(UserContact $uc)
    {
        $uc->delete();
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
            ->where('user_id', Auth::user()->id)
            ->whereHas('contact', function ($q) {
                $q->where('active', 1);
            })
            ->get();
        // dd($this->contacts);
        return view('livewire.pages.contact.index');
    }
}
