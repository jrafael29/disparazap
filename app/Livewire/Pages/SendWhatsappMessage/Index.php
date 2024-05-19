<?php

namespace App\Livewire\Pages\SendWhatsappMessage;

use App\Models\Contact;
use App\Models\UserContact;
use Livewire\Component;

class Index extends Component
{
    public Contact $contact;
    public function mount(Contact $contact)
    {
        $this->contact = $contact;
    }
    public function render()
    {
        return view('livewire.pages.send-whatsapp-message.index');
    }
}
