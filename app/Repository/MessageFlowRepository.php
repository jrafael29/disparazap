<?php

namespace App\Repository;

use App\Models\MessageFlow;

class MessageFlowRepository
{
    private MessageFlow $model;
    function __construct()
    {
        $this->model = MessageFlow::query();
    }

    public function createMessageFlow(int $userId, string $description)
    {
        $messageFlow = $this->model->create([
            'user_id' => $userId,
            'description' => $description
        ]);
        dd($messageFlow);
    }
}
