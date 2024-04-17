<?php

namespace App\Service\Evolution;

use App\Helpers\ArrayHelper;
use App\Helpers\Phonenumber;
use Error;
use Exception;
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

    // public function divideArrayItems($items, $itemsPerBatch = 50)
    // {
    //     $subArrays = [];
    //     $count = 0;
    //     $totalItems = count($items);
    //     for ($i = 0; $i < $totalItems; $i += $itemsPerBatch) {
    //         $subArrays[] = array_slice($items, $i, $itemsPerBatch);
    //     }
    //     // dd($subArrays);
    //     return $subArrays;
    // }

    public function makeManyRequests()
    {
        // se
    }

    public function checkNumbers($instanceName, $numbers)
    {
        if (empty($numbers)) return false;
        $getNumbersRoute = '/chat/whatsappNumbers/' . $instanceName;
        $url = $this->apiUrl . $getNumbersRoute;

        $headers = [
            'apiKey' => $this->apiKey
        ];
        $body = ['numbers' => $numbers];
        $response = Http::withHeaders($headers)->post($url, $body);
        $data = $response->json();
        if (!empty($data['error'])) {
            throw new Exception(json_encode($data['response']), $data['status']);
        }
        return $this->formatNumbers($data);
    }

    public function checkNumbersExistence($instanceName, $numbers = [])
    {
        try {


            if (empty($numbers) || !$instanceName) {
                return false;
            }
            $numberBatches = [];
            $maxPhonenumbersPerRequest = 50;
            if (count($numbers) > $maxPhonenumbersPerRequest) {
                $numberBatches = ArrayHelper::divideArrayItems($numbers, $maxPhonenumbersPerRequest);
            } elseif (count($numbers) == 1) {
                $numberBatches[] = $numbers;
            } else {
                $numberBatches = $numbers;
            }

            // Faz uma requisição para cada lote de números

            $formattedNumbers = [];

            if ($numberBatches > 1) {
                foreach ($numberBatches as $batch) {
                    // $response = Http::withHeaders($headers)->post($url, $body);
                    // $data = $response->json();
                    // // dd($data);
                    // if (!empty($data['error'])) {
                    //     throw new Exception(json_encode($data['response']), $data['status']);
                    // }
                    $data = $this->checkNumbers(
                        instanceName: $instanceName,
                        numbers: $batch
                    );
                    $formattedNumbers += $data;
                }
            } else {
            }


            return $formattedNumbers;
        } catch (\Exception $e) {
            return false;
        }
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
