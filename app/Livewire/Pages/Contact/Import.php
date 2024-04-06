<?php

namespace App\Livewire\Pages\Contact;

use Livewire\Component;

class Import extends Component
{
    public $importOption = '';
    public $importOptions = [
        'group-contacts' => 'Grupos do whatsapp',
        'raw-text' => "Colar texto",
        // 'import-excel' => "Importar excel"
    ]; // opções de "alvos"

    public function changeImportOption($option)
    {
        $this->importOption = $option;
    }

    public function render()
    {
        return view('livewire.pages.contact.import');
    }
}
