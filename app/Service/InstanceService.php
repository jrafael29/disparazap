<?php

namespace App\Service;

use App\Helpers\Base64ToFile;
use App\Models\Instance;
use App\Repository\InstanceRepository;
use App\Service\Evolution\EvolutionInstanceService;
use Illuminate\Support\Facades\Storage;

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


    function updateQrInstance($instanceName)
    {
        try {
            $instanceModel = Instance::query()->where('name', $instanceName)->first();
            if (!$instanceModel) return false;

            $instanceState = $this->evolutionInstanceService->getStateInstance($instanceName);
            if ($instanceState === 'open') {
                return false;
            }

            if ($instanceState === 'close') {
                return false;
            }

            if (!empty($instanceModel->qrcode_path)) {
                // remove existente qrcode;
                Storage::delete('public/' . $instanceModel->qrcode_path);
                $instanceModel->qrcode_path = '';
                $instanceModel->save();
            }

            $instanceData = $this->evolutionInstanceService->connectInstance($instanceName);

            if (empty($instanceData['base64'])) return false;

            $filename = Base64ToFile::storeImageFromBase64($instanceData['base64'], 'qrcodes/qr_' . uniqid() . '.png');

            $this->instanceRepository->updateInstance($instanceName, [
                'qrcode_path' => $filename
            ]);
            return $filename;
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
