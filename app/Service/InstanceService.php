<?php

namespace App\Service;

use App\Repository\InstanceRepository;
use App\Service\Evolution\EvolutionInstanceService;

class InstanceService
{

    function __construct(
        private InstanceRepository $instanceRepository,
        private EvolutionInstanceService $evolutionInstanceService
    ) {
    }

    function deleteInstance($instanceName)
    { {
            $this->instanceRepository->deleteInstanceByName($instanceName);
            $instanceState = $this->evolutionInstanceService->getStateInstance($instanceName);
            if ($instanceState === 'open') {
                $this->evolutionInstanceService->logoutInstance($instanceName);
            }
            $this->evolutionInstanceService->removeInstance($instanceName);
        }
    }
}
