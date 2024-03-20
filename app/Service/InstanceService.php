<?php

namespace App\Service;

use App\Helpers\Base64ToFile;
use App\Models\Instance;
use App\Repository\InstanceRepository;
use App\Service\Evolution\EvolutionInstanceService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InstanceService
{

    function __construct(
        private InstanceRepository $instanceRepository,
        private EvolutionInstanceService $evolutionInstanceService
    ) {
    }

    function getInstance($instanceName)
    {

        try {
            $cacheKey = 'instanceData:' . $instanceName;
            $cachedPayback = Cache::get($cacheKey);

            if (!$cachedPayback) {
                $instanceData = $this->evolutionInstanceService->getInstance($instanceName);
                if (!$instanceData) {
                    $payback = [
                        'error' => true,
                        'message' => "Instancia não encontrada"
                    ];
                    return $payback;
                }
                $payback = [
                    'error' => false,
                    'data' => [
                        'profilePictureUrl' => $instanceData['profilePictureUrl'],
                        'profileName' => $instanceData['profileName'],
                        'profileStatus' => $instanceData['profileStatus'],
                    ]
                ];
                Cache::add($cacheKey, $payback, (int) env('CACHE_DEFAULT_LIFETIME'));
                return $payback;
            }
            return $cachedPayback;
        } catch (\Exception $e) {
            Log::info($e->getMessage());

            return false;
        }
    }

    function createInstance($userId, $description, $phonenumber)
    {
        try {
            $instanceModel = $this->instanceRepository->createInstance(
                userId: $userId,
                description: $description,
                phonenumber: $phonenumber
            );

            $evolutionInstanceData = $this->evolutionInstanceService->createInstance(
                instanceName: $instanceModel->name,
                phonenumber: $instanceModel->phonenumber
            );
            $this->evolutionInstanceService->setWebhooks(
                instanceName: $instanceModel->name,
                // endPoint: '/updated-connection/webhook'
            );
            if (!empty($evolutionInstanceData['base64'])) {
                // $result = $this->updateQrInstance($instanceModel->name);
                $filename = $this->updateQrInstanceHelper(base64: $evolutionInstanceData['base64'], instanceName: $instanceModel->name);

                if ($filename) {
                    $instanceModel->qrcode_path = $filename;
                    $instanceModel->save();
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return false;
        }
    }


    function deleteInstance($instanceName)
    {
        try {
            $instanceModel = Instance::query()->where('name', $instanceName)->first();
            if (!$instanceModel) {
                return [
                    'error' => true,
                    'message' => "Instancia não encontrada"
                ];
            }

            Storage::delete('public/' . $instanceModel->qrcode_path);
            $this->instanceRepository->deleteInstanceByName($instanceModel->name);
            $instanceState = $this->evolutionInstanceService->getStateInstance($instanceModel->name);
            if ($instanceState === 'open') {
                $this->evolutionInstanceService->logoutInstance($instanceModel->name);
            }
            $this->evolutionInstanceService->removeInstance($instanceModel->name);
            return [
                'error' => false,
                'data' => [
                    'success' => true
                ]
            ];
        } catch (\Exception $e) {
            Log::info($e->getMessage());


            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }


    function updateQrInstanceHelper($base64, $instanceName)
    {
        $filename = 'qrcodes/qr_' . uniqid() . '.png';
        $storedFilename = Base64ToFile::storeImageFromBase64($base64, $filename);

        $this->instanceRepository->updateInstance($instanceName, [
            'qrcode_path' => $storedFilename
        ]);

        return $storedFilename;
    }

    function updateQrInstance($instanceName)
    {
        try {
            $instanceModel = Instance::query()->where('name', $instanceName)->first();
            if (!$instanceModel) return false;
            if (!empty($instanceModel->qrcode_path)) {
                // remove existente qrcode;
                Storage::delete('public/' . $instanceModel->qrcode_path);
                $instanceModel->qrcode_path = '';
                $instanceModel->save();
            }

            // $instanceState = ['open' || 'close' || 'connecting']
            $instanceState = $this->evolutionInstanceService->getStateInstance($instanceName);
            if ($instanceState === 'open') {
                return ['error' => true, 'message' => "Instance already opened."];
            }

            if ($instanceState === 'close') {
                // return ['error' => true, 'message' => "Instance closed."];
            }

            $instanceData = $this->evolutionInstanceService->connectInstance($instanceName);

            if (empty($instanceData['base64'])) return ['error' => true, 'message' => "Internal server Error."];

            $this->evolutionInstanceService->setWebhooks(
                instanceName: $instanceModel->name,
                // endPoint: '/updated-qrcode/webhook',
                // webhooks: ["QRCODE_UPDATED"]
            );


            $filename = $this->updateQrInstanceHelper(base64: $instanceData['base64'], instanceName: $instanceModel->name);
            // $filename = 'qrcodes/qr_' . uniqid() . '.png';
            // $storedFilename = Base64ToFile::storeImageFromBase64($instanceData['base64'], $filename);

            $this->instanceRepository->updateInstance($instanceName, [
                'qrcode_path' => $filename
            ]);

            return ['error' => false, 'data' => ['filename' => $filename]];
            // return $filename;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }
}
