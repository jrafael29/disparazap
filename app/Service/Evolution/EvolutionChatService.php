<?php

namespace App\Service\Evolution;

use App\Helpers\Phonenumber;
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



        // Faz uma requisição para cada lote de números
        $getNumbersRoute = '/chat/whatsappNumbers/' . $instanceName;
        $url = $this->apiUrl . $getNumbersRoute;

        $headers = [
            'apiKey' => $this->apiKey
        ];

        $formattedNumbers = [];
        foreach ($numberBatches as $batch) {
            $body = ['numbers' => $batch];
            $response = Http::withHeaders($headers)->post($url, $body);
            $data = $response->json();
            $formattedNumbers += $this->formatNumbers($data);
        }
        return $formattedNumbers;
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
