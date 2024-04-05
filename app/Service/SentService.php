<?php

namespace App\Service;

use App\Helpers\Phonenumber as PhonenumberHelper;
use App\Models\Sent;
use App\Repository\SentRepository;
use App\Traits\ServiceResponseTrait;
use Carbon\Carbon;

class SentService
{
    use ServiceResponseTrait;
    public function __construct(
        private FlowToSentService $flowToSentService,
        private SentRepository $sentRepository
    ) {
    }
    public function scheduleSent($userId, $flowId, $instances, $phonenumbers, $sendDate, $delay = 15, $description)
    {
        $sent = Sent::query()->create([
            'user_id' => $userId,
            'description' => $description
        ]);

        $allInstancesPhonenumbers = PhonenumberHelper::dividePhonenumbersByInstances(
            instances: $instances,
            phonenumbers: $phonenumbers,
        );
        foreach ($allInstancesPhonenumbers as $instanceId => $instancePhonenumbers) {
            $this->scheduleInstanceSents(
                sentId: $sent->id,
                instanceId: $instanceId,
                phonenumbers: $instancePhonenumbers,
                sendDate: $sendDate,
                delay: $delay,
                userId: $userId,
                flowId: $flowId
            );
        }
    }

    public function scheduleInstanceSents(
        $sentId,
        $instanceId,
        $phonenumbers,
        $sendDate,
        $delay,
        $userId,
        $flowId
    ) {
        if (!$sentId || !$instanceId || !count($phonenumbers) || !$sendDate || !$delay || !$userId || !$flowId) return false;
        foreach ($phonenumbers as $index => $phonenumber) {
            $toSentDate = Carbon::parse($sendDate)->addSeconds(($delay * $index) + 5);
            // dd($sendDate);
            $this->flowToSentService->createFlowToSent(
                userId: $userId,
                flowId: $flowId,
                sentId: $sentId,
                phonenumber: $phonenumber,
                instanceId: $instanceId,
                sendAt: $toSentDate,
                delayInSeconds: $delay
            );
        }

        return true;
    }

    public function pauseSent($sentId)
    {
        try {
            $result = $this->sentRepository->pauseSent($sentId);
            if ($result) {
                return $this->successResponse(data: [
                    'paused' => true
                ]);
            }
        } catch (\Exception $e) {
            report($e->getMessage());
            return $this->errorResponse(message: $e->getMessage(), statusCode: 500);
        }
    }

    public function playSent($sentId)
    {
        try {
            $result = $this->sentRepository->playSent($sentId);
            if ($result) {
                return $this->successResponse(data: [
                    'play' => true
                ]);
            }
        } catch (\Exception $e) {
            report($e->getMessage());
            return $this->errorResponse(message: $e->getMessage(), statusCode: 500);
        }
    }
}
