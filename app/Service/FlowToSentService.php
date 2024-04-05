<?php

namespace App\Service;

use App\Models\Contact;
use App\Models\FlowToSent;
use App\Models\User;
use App\Models\UserContact;
use App\Traits\ServiceResponseTrait;
use Illuminate\Support\Facades\Log;

class FlowToSentService
{
    use ServiceResponseTrait;
    public function createFlowToSent($userId, $flowId, $sentId, $phonenumber, $instanceId, $sendAt, $delayInSeconds)
    {
        try {
            $contact = Contact::query()->firstOrCreate([
                'phonenumber' => $phonenumber
            ], [
                'phonenumber' => $phonenumber,
                'description' => 'Meu contato'
            ]);
            $userContact = UserContact::query()->firstOrCreate([
                'user_id' => $userId,
                'contact_id' => $contact->id
            ]);

            $flowToSent = FlowToSent::query()->create([
                'user_id' => $userId,
                'flow_id' => $flowId,
                'instance_id' => $instanceId,
                'contact_id' => $contact->id,
                'sent_id' => $sentId,
                "to" => $phonenumber,
                "to_sent_at" => $sendAt,
                'delay_in_seconds' => $delayInSeconds,
            ]);

            return $this->successResponse(data: [
                'flowToSent' => $flowToSent
            ], statusCode: 201);
        } catch (\Exception $e) {
            dd($e);
            report($e);
            return $this->errorResponse('Erro interno', 500);
            // Log::info("createFlowToSent: {$e->getMessage()}");
            // return [
            //     'error' => true,
            //     'message' => $e->getMessage()
            // ];
        }
    }
}
