<?php

namespace App\Service\Evolution;

use Illuminate\Support\Facades\Http;

class EvolutionInstanceService
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

    function createInstance($instanceName, $phonenumber, $token)
    {
        try {
            $createInstanceRoute = '/instance/create';
            $url = $this->apiUrl . $createInstanceRoute;
            $body = [
                'instanceName' => $instanceName,
                'number' => $phonenumber,
                'qrcode' => true,
                'token' => $token
            ];

            $headers = [
                'apiKey' => $this->apiKey
            ];

            $response = Http::withHeaders($headers)
                ->post($url, $body);

            $data = $response->body();
            // dd($data);
            $instance = $response->json('instance');
            if (!$instance) {
                return false;
            }
            $instanceApiKey = $response->json('hash')['apikey'];
            $qr = $response->json('qrcode');
            $instanceData = [
                'apikey' => $instanceApiKey,
                'instance' => $instance,
                'base64' => $qr['base64']
            ];
            return $instanceData;
        } catch (\Exception $e) {
            dd($e);
            report($e);
        }
    }
    function getStateInstance($instanceName)
    {

        $createInstanceRoute = '/instance/connectionState/' . $instanceName;
        $url = $this->apiUrl . $createInstanceRoute;
        $headers = [
            'apiKey' => $this->apiKey
        ];
        $response = Http::withHeaders($headers)->get($url . '?instanceName=' . $instanceName);

        if ($response->json('instance')) {
            return $response->json('instance')['state'];
        }

        if ($response->json('status')) {
            switch ($response->json('status')) {
                case (404):
                    return false;
                    break;
            }
        }
    }

    function getInstance($instanceName)
    {
        $createInstanceRoute = '/instance/fetchInstances';
        $url = $this->apiUrl . $createInstanceRoute;
        $headers = [
            'apiKey' => $this->apiKey
        ];
        $response = Http::withHeaders($headers)->get($url);

        if ($response->body()) {
            $instanceData = [];
            foreach ($response->json() as $item) {
                $instance = $item['instance'];
                if ($instance['status'] == 'open') {
                    if ($instance['instanceName'] == $instanceName) {
                        $instanceData = $instance;
                    }
                }
            }

            if (!$instanceData) {
                return false;
            }
            return $instanceData;
        }
    }

    function connectInstance($instanceName)
    {
        try {
            $createInstanceRoute = '/instance/connect/' . $instanceName;
            $url = $this->apiUrl . $createInstanceRoute;

            $headers = [
                'apiKey' => $this->apiKey
            ];
            $response = Http::withHeaders($headers)->get($url);
            $base64 = $response->json('base64');
            if ($base64) {
                $instanceData = [
                    'base64' => $base64
                ];
                return $instanceData;
                // $this->dispatch("update-qr", $instanceData);
            }
            $instance = $response->json('instance');
            if ($instance['state'] == 'open') {
                return $instance;
            }

            return false;
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
        // dd($response->body());
    }

    function setWebhooks($instanceName, $endPoint, $webhooks = ["CONNECTION_UPDATE"])
    {
        try {
            $createInstanceRoute = '/webhook/set/' . $instanceName;
            $url = $this->apiUrl . $createInstanceRoute;
            $body = [
                "enabled" => true,
                "url" => $this->webhookUrl . $endPoint,
                "webhookByEvents" => true,
                "events" => $webhooks
            ];

            $headers = [
                'apiKey' => $this->apiKey
            ];

            $response = Http::withHeaders($headers)
                ->post($url, $body);

            $data = $response->body();
            return true;
            // dd($data);
            // $this->dispatch("update-qr", $instanceData);
        } catch (\Exception $e) {
            dd($e);
        }
    }
    function removeInstance($instanceName)
    {
        try {

            $createInstanceRoute = '/instance/delete/' . $instanceName;
            $url = $this->apiUrl . $createInstanceRoute;

            $headers = [
                'apiKey' => $this->apiKey
            ];

            $response = Http::withHeaders($headers)
                ->delete($url);
            if ($response->json('error') == false) {
                return true;
            }
            return false;
            // $this->dispatch("update-qr", $instanceData);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    function logoutInstance($instanceName)
    {
        try {

            $createInstanceRoute = '/instance/logout/' . $instanceName;
            $url = $this->apiUrl . $createInstanceRoute;

            $headers = [
                'apiKey' => $this->apiKey
            ];

            $response = Http::withHeaders($headers)
                ->delete($url);
            if ($response->json('error') == false) {
                return true;
            }
            return false;
            // $this->dispatch("update-qr", $instanceData);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
