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
            Cache::add($groupsKeyName, $groups, (int) env('CACHE_DEFAULT_LIFETIME'));
            return $groups;
        }
        return $cachedData;
    }

    function getParticipantsByJid($instanceName, $groupJid)
    {
        if (!$instanceName) return false;

        $groupParticipantsKeyName = "$instanceName:group-$groupJid:participants";
        $cachedData = Cache::get($groupParticipantsKeyName);

        if (!$cachedData) {
            $getGroupParticipantsRoute = '/group/participants/' . $instanceName . '?groupJid=' . $groupJid;
            $url = $this->apiUrl . $getGroupParticipantsRoute;
            $headers = [
                'apiKey' => $this->apiKey
            ];
            $response = Http::withHeaders($headers)->get($url);
            $data = $response->json();
            if (!empty($data['participants'])) {
                $payback = [
                    'error' => false,
                    'data' => [
                        $groupJid => $data['participants']
                    ]
                ];
                Cache::add($groupParticipantsKeyName, $payback, (int) env('CACHE_DEFAULT_LIFETIME'));
                return $payback;
            }

            return [
                'error' => true,
                'message' => "foobar"
            ];
        }
        return $cachedData;
    }
}
