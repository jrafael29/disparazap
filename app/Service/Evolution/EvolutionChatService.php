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

    public function checkNumbersExistence($instanceName, $numbers = [])
    {
        if (!count($numbers)) {
            return false;
        }

        $getNumbersRoute = '/chat/whatsappNumbers/' . $instanceName;
        $url = $this->apiUrl . $getNumbersRoute;
        $body = [
            'numbers' => $numbers,
        ];

        $headers = [
            'apiKey' => $this->apiKey
        ];

        $response = Http::withHeaders($headers)
            ->post($url, $body);
        $data = $response->json();
        return $this->formatNumbers($data);
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
