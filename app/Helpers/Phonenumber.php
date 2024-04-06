<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Phonenumber
{
    static public function getPhonenumbersFromText($text, $allowRepeated = false): array
    {
        try {
            $regex = '/(?:55)?(\d{2})?(\d{4,5})(\d{4})\b/';
            $phonenumbers = [];

            // Executar a expressão regular na string
            preg_match_all($regex, $text, $matches);

            // Adicionar os telefones encontrados ao array
            foreach ($matches[0] as $match) {
                $phonenumbers[] = $match;
            }
            if (!$allowRepeated) {
                return self::filterUniquePhonenumbers(array_values($phonenumbers));
            }
            return $phonenumbers;
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
    }

    static public function addDdiToPhonenumbers($phonenumbers = [], $ddi = 55): array
    {
        if (empty($phonenumbers)) return false;
        return array_map(function ($phone) use ($ddi) {
            if (strlen($phone) < 10 || strlen($phone) > 11) return $phone;
            // Verifica se o número começa com o DDI 55 e se não tem o prefixo '55'
            return $ddi . $phone;
        }, $phonenumbers);
    }

    static public function filterUniquePhonenumbers($phonenumbers = [])
    {
        if (empty($phonenumbers)) return [];

        // dd("uniq", $phonenumbers);
        $correctlyPhonenumbers = [];
        foreach ($phonenumbers as $phonenumber) {
            // pega os ultimos 8 digitos do telefone.
            $PhonenumberWithoutDDs = substr($phonenumber, -8);
            if (!empty($correctlyPhonenumbers)) {
                $result = $correctlyPhonenumbers[$PhonenumberWithoutDDs] ?? false;
                if ($result) continue;
                $correctlyPhonenumbers[$PhonenumberWithoutDDs] = $phonenumber;
            } else {
                $correctlyPhonenumbers[$PhonenumberWithoutDDs] = $phonenumber;
            }
        }
        return array_values($correctlyPhonenumbers);
    }

    static public function getPhonenumberFromParticipant($participant = [], $ddi = 0)
    {
        // Se $ddi for 0, retornar todos os números
        if ($ddi == 0) {
            if (!empty($participant['id'])) {
                // Extrair o número de telefone do ID
                $number = explode('@', $participant['id'])[0];
                return $number;
            }
            return false;
        }

        // Se $ddi for 55, filtrar apenas os números brasileiros
        if ($ddi == 55) {
            if (!empty($participant['id'])) {
                // Extrair o número de telefone do ID
                $number = explode('@', $participant['id'])[0];
                // Verificar se o número começa com 55
                if (preg_match('/^55\d{0,11}$/', $number)) {
                    return $number;
                }
            }
            return false;
        }

        // Caso $ddi seja diferente de 0 e 55, retornar falso
        return false;
    }

    static public function getPhonenumbersFromGroupsParticipants($groupsParticipantsPhonenumber)
    {
        $numbers = [];
        // dd($this->groupsParticipantsPhonenumber);
        foreach ($groupsParticipantsPhonenumber as $groups) {
            foreach ($groups as $groupJid => $participants) {
                foreach ($participants as $participantNumber)
                    array_push($numbers, $participantNumber);
            }
        }
        return $numbers;
    }


    static public function dividePhonenumbersByInstances($instances = [], $phonenumbers = [])
    {
        if (empty($instances) || empty($phonenumbers)) return false;
        $numbersPerInstance = count($phonenumbers) / count($instances);
        $allInstancesPhonenumbers = [];
        $offset = 0;
        foreach ($instances as $index => $instance) {
            if ($index + 1 === count($instances))
                $allInstancesPhonenumbers[$instance] = array_slice($phonenumbers, $offset, $numbersPerInstance + 1);
            else
                $allInstancesPhonenumbers[$instance] = array_slice($phonenumbers, $offset, $numbersPerInstance);

            $offset = $offset + $numbersPerInstance;
        }
        return $allInstancesPhonenumbers;
    }

    static public function addDddToPhonenumbers($phonenumbers = [], $ddd)
    {
        if (empty($phonenumbers) || empty($ddd)) return false;
        return array_map(function ($phone) use ($ddd) {
            // 1. O numero deve ter 8 ou 9 de tamanho.
            if (strlen($phone) < 8 || strlen($phone) > 9) return $phone;

            return $ddd . $phone;
        }, $phonenumbers);
    }

    static public function divideNumberExistence($phonenumbersWithExistence = [])
    {
        if (empty($phonenumbersWithExistence)) return false;

        $existentPhonenumbers = [];
        $inexistentPhonenumbers = [];
        foreach ($phonenumbersWithExistence as $phonenumber => $exist) {
            // like be: '5581991931921' => true
            if ($exist) {
                array_push($existentPhonenumbers, $phonenumber);
            } else {
                array_push($inexistentPhonenumbers, $phonenumber);
            }
        }

        return [
            'existents' => $existentPhonenumbers,
            'inexistents' => $inexistentPhonenumbers
        ];
    }
}
