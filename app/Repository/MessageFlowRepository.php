<?php

namespace App\Repository;

use App\Models\MessageFlow;
use Illuminate\Support\Str;

class MessageFlowRepository
{

    public function createMessageFlow(int $userId, string $description)
    {
        $messageFlow = MessageFlow::query()->create([
            'user_id' => $userId,
            'description' => Str::of($description)->trim()
        ]);
        return true;
    }
}
