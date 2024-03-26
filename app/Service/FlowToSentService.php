<?php

namespace App\Service;

use App\Models\Contact;
use App\Models\FlowToSent;
use App\Models\User;
use App\Models\UserContact;
use Illuminate\Support\Facades\Log;

class FlowToSentService
{

    public function createFlowToSent($userId, $flowId, $phonenumber, $instanceId, $sendAt, $delayInSeconds)
    {
        try {
            $contact = Contact::query()->firstOrCreate([
                'phonenumber' => $phonenumber
            ], [
                'phonenumber' => $phonenumber,
                'description' => 'Meu contato'
            ]);
            UserContact::query()->create([
                'user_id' => $userId,
                'contact_id' => $contact->id
            ]);
            $flowToSent = FlowToSent::query()->create([
                'user_id' => $userId,
                'flow_id' => $flowId,
                'instance_id' => $instanceId,
                "to" => $phonenumber,
                "to_sent_at" => $sendAt,
                'delay_in_seconds' => $delayInSeconds,
            ]);
            return [
                'error' => false,
                'data' => [
                    'flowToSent' => $flowToSent
                ]
            ];
        } catch (\Exception $e) {
            Log::info("createFlowToSent: {$e->getMessage()}");
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }
}
