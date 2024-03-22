<?php

namespace App\Service\Evolution;

use Illuminate\Support\Facades\Http;

class EvolutionChatService
{
    private $apiKey;
    private $apiUrl;
    private $webhookUrl;

    function __construct()
    {
        $this->apiUrl = env('EVOLUTION_API_URL');
        $this->apiKey = env('EVOLUTION_API_KEY');
        $this->webhookUrl = env("WEBHOOK_URL");
    }

    public function divideArrayItems($items, $itemsPerBatch = 50)
    {
        $subArrays = [];
        $count = 0;
        $totalItems = count($items);

        for ($i = 0; $i < $totalItems; $i += $itemsPerBatch) {
            $subArrays[] = array_slice($items, $i, $itemsPerBatch);
        }

        // dd($subArrays);
        return $subArrays;
    }

    public function makeManyRequests()
    {
        // se

    }

    public function checkNumbersExistence($instanceName, $numbers = [])
    {
        if (!count($numbers)) {
            return false;
        }

        $maxPhonenumbersPerRequest = 50;
        if (count($numbers) >= $maxPhonenumbersPerRequest) {
            $this->divideArrayItems($numbers, 50);
        }

        $numberBatches = $this->divideArrayItems($numbers);

        $formattedNumbers = [];

        // Faz uma requisição para cada lote de números
        $getNumbersRoute = '/chat/whatsappNumbers/' . $instanceName;
        $url = $this->apiUrl . $getNumbersRoute;
        $body = [
            'numbers' => $numbers,
        ];

        $headers = [
            'apiKey' => $this->apiKey
        ];
        foreach ($numberBatches as $batch) {
            $body = ['numbers' => $batch];

            $response = Http::withHeaders($headers)->post($url, $body);
            $data = $response->json();
            $formatedPhonenumbers = $this->formatNumbers($data);
            // dd($phonenumbersFormated);
            // Formata os números retornados
            $formattedNumbers += $this->formatNumbers($data);
            // $formattedNumbers = array_merge($formattedNumbers, $formatedPhonenumbers);
        }
        // dd($formattedNumbers);

        return $formattedNumbers;


        // $getNumbersRoute = '/chat/whatsappNumbers/' . $instanceName;
        // $url = $this->apiUrl . $getNumbersRoute;
        // $body = [
        //     'numbers' => $numbers,
        // ];

        // $headers = [
        //     'apiKey' => $this->apiKey
        // ];

        // $response = Http::withHeaders($headers)
        //     ->post($url, $body);
        // $data = $response->json();
        // return $this->formatNumbers($data);
    }

    private function formatNumbers($numbersInfo = [])
    {
        if (!count($numbersInfo)) return false;
        $numbers = [];
        foreach ($numbersInfo as $numberInfo) {
            $jid = $numberInfo['jid'];
            $phonenumber = str_replace('+', '', explode('@', $jid)[0]);

            $numbers[$phonenumber] = $numberInfo['exists'];
        }
        return $numbers;
    }
}
