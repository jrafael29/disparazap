<?php

namespace App\Service\Evolution;

use App\Models\EvolutionInstance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionSendMessageService
{
    private $apiKey;
    private $apiUrl;
    function __construct()
    {
        $this->apiUrl = env('EVOLUTION_API_URL');
        $this->apiKey = env('EVOLUTION_API_KEY');
    }

    function sendText($instanceName, $text, $to,  $delay = 1200)
    {
        try {
            $createInstanceRoute = '/message/sendText/' . $instanceName;
            $url = $this->apiUrl . $createInstanceRoute;
            $body = [
                "number" => $to,
                "textMessage" => ["text" => $text],
                "options" => [
                    "delay" => (int)$delay,
                    "presence" => "composing",
                    "linkPreview" => true,
                ]
            ];

            $headers = [
                'apiKey' => $this->apiKey
            ];

            $response = Http::withHeaders($headers)
                ->post($url, $body);

            $data = $response->body();
            Log::info("request send text completed");
            Log::info($data);
        } catch (\Exception $e) {
            dd($e);
            report($e);
        }
    }

    function sendImage($instanceName, $imageBase64OrUrl, $text, $to,  $delay = 1200)
    {
        try {
            $createInstanceRoute = '/message/sendMedia/' . $instanceName;
            $url = $this->apiUrl . $createInstanceRoute;
            $body = [
                "number" => $to,
                "mediaMessage" => [
                    "mediatype" => "image",
                    "caption" => $text,
                    "media" => $imageBase64OrUrl
                ],
                "options" => [
                    "delay" => (int)$delay,
                    "presence" => "composing",
                    "linkPreview" => true,
                ]
            ];

            $headers = [
                'apiKey' => $this->apiKey
            ];

            $response = Http::withHeaders($headers)
                ->post($url, $body);

            $data = $response->body();
        } catch (\Exception $e) {
            dd($e);
            report($e);
        }
    }

    function sendVideo($instanceName, $videoBase64OrUrl, $text, $to,  $delay = 1200)
    {
        try {
            $createInstanceRoute = '/message/sendMedia/' . $instanceName;
            $url = $this->apiUrl . $createInstanceRoute;
            $body = [
                "number" => $to,
                "mediaMessage" => [
                    "mediatype" => "video",
                    "caption" => $text,
                    "media" => $videoBase64OrUrl
                ],
                "options" => [
                    "delay" => (int)$delay,
                    "presence" => "composing",
                    "linkPreview" => true,
                ]
            ];

            $headers = [
                'apiKey' => $this->apiKey
            ];

            $response = Http::withHeaders($headers)
                ->post($url, $body);

            $data = $response->body();
        } catch (\Exception $e) {
            dd($e);
            report($e);
        }
    }
}
