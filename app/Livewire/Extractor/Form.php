<?php

namespace App\Livewire\Extractor;

use App\Helpers\Phonenumber as PhonenumberHelper;
use Livewire\Component;

class Form extends Component
{
    public $text;
    public $includeDdi = true;
    public $encouteredPhonenumbers = [];

    public function handleSubmit()
    {
        if (empty($this->text)) return false;

        // $result = $this->extractPhonenumbersFromString($this->text);
        $result = PhonenumberHelper::getPhonenumbersFromText($this->text);

        if ($this->includeDdi) {
            $result = PhonenumberHelper::addDdiToPhonenumbers(phonenumbers: $result, ddi: 55);
            // $result = $this->includeDdiToPhonenumbers(55, $result);
        }

        $this->encouteredPhonenumbers = $result;
    }

    // function includeDdiToPhonenumbers($ddi, $phonenumbers = [])
    // {
    //     return array_map(function ($phone) use ($ddi) {
    //         // Verifica se o número começa com o DDI 55 e se não tem o prefixo '55'
    //         if (strpos($phone, $ddi) !== 0) {
    //             // Adiciona o prefixo '55' ao número de telefone
    //             return "55" . $phone;
    //         }
    //         return $phone;
    //     }, $phonenumbers);
    // }

    // function extractPhonenumbersFromString($string)
    // {
    //     // Expressão regular para encontrar números seguindo o padrão "55" seguido por uma sequência de dígitos
    //     $regex = '/(?:55)?(\d{2})(\d{4,5})(\d{4})\b/';
    //     $phonenumbers = [];

    //     // Executar a expressão regular na string
    //     preg_match_all($regex, $string, $matches);

    //     // Adicionar os telefones encontrados ao array
    //     foreach ($matches[0] as $match) {
    //         $phonenumbers[] = $match;
    //     }

    //     return $phonenumbers;
    // }


    public function render()
    {
        return view('livewire.extractor.form');
    }
}
