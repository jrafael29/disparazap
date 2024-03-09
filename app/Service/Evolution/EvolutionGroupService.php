<?php

namespace App\Service\Evolution;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EvolutionGroupService
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

    function getGroups($instanceName, bool $withParticipants = false)
    {
        if (!$instanceName) return false;

        $groupsKeyName = $instanceName . ':groups';
        $cachedData = Cache::get($groupsKeyName);

        if (!$cachedData) {
            $createInstanceRoute = '';
            if ($withParticipants) {
                $createInstanceRoute = '/group/fetchAllGroups/' . $instanceName . '?getParticipants=true';
            } else {
                $createInstanceRoute = '/group/fetchAllGroups/' . $instanceName . '?getParticipants=false';
            }
            $url = $this->apiUrl . $createInstanceRoute;

            $headers = [
                'apiKey' => $this->apiKey
            ];
            $response = Http::withHeaders($headers)->get($url);

            $groups = $response->json();
            Cache::add($groupsKeyName, $groups, 600);
            return $groups;
        } else {
            return $cachedData;
        }
    }
}
