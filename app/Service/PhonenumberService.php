<?php


namespace App\Service;

use App\Helpers\ArrayHelper;
use App\Models\Instance;
use App\Service\Evolution\EvolutionChatService;
use App\Traits\ServiceResponseTrait;

class PhonenumberService
{
    use ServiceResponseTrait;
    public function __construct(private EvolutionChatService $evolutionChatService)
    {
    }

    public function verifyPhonenumbersExistence($userId, $phonenumbers = [])
    {
        // $instances = ['2-instance-1', '2-instance-2', '2-instance-3'];
        $instancesCount = Instance::query()->where('user_id', $userId)->count();

        if (empty($phonenumbers) || empty($instances)) return $this->errorResponse(
            message: "invalid parameters"
        );
        // phonenumber = 100.000 length   => phonenumbers can be min 1 max N
        // $instances = 5 length          => instaces can be min 1 max N

        $phonenumbersLength = count($phonenumbers);
    }
}
