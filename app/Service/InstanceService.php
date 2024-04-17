<?php

namespace App\Service;

use App\Helpers\Base64ToFile;
use App\Models\Instance;
use App\Repository\InstanceRepository;
use App\Service\Evolution\EvolutionInstanceService;
use App\Traits\ServiceResponseTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InstanceService
{
    use ServiceResponseTrait;
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

    function createEvolutionInstance($instanceName, $phonenumber)
    {
        $evolutionInstanceData = $this->evolutionInstanceService->createInstance(
            instanceName: $instanceName,
            phonenumber: $phonenumber
        );
        $this->evolutionInstanceService->setWebhooks(
            instanceName: $instanceName,
            // endPoint: '/updated-connection/webhook'
        );
        return $evolutionInstanceData;
    }

    function createInstance($userId, $description, $phonenumber)
    {
        try {
            $instanceModel = $this->instanceRepository->createInstance(
                userId: $userId,
                description: $description,
                phonenumber: $phonenumber
            );

            $evolutionInstanceData = $this->createEvolutionInstance(
                phonenumber: $instanceModel->phonenumber,
                instanceName: $instanceModel->name
            );

            // $evolutionInstanceData = $this->evolutionInstanceService->createInstance(
            //     instanceName: $instanceModel->name,
            //     phonenumber: $instanceModel->phonenumber
            // );
            // $this->evolutionInstanceService->setWebhooks(
            //     instanceName: $instanceModel->name,
            //     // endPoint: '/updated-connection/webhook'
            // );
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

    function logoutInstance($instanceName)
    {
        $this->evolutionInstanceService->logoutInstance($instanceName);
        $this->instanceRepository->updateInstance($instanceName, [
            'online' => 0
        ]);

        return $this->successResponse([
            'success' => true
        ]);
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
            $this->evolutionInstanceService->removeInstance($instanceModel->name);
            $this->instanceRepository->deleteInstanceByName($instanceModel->name);

            return $this->successResponse(data: [
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());

            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: 500
            );
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
            $instanceData = ''; // ponteiro para os dados com qrcode da instancia;

            $instanceStateOnEvolution = $this->getInstanceState($instanceName);
            if (!$instanceStateOnEvolution) {
                $instanceData = $this->createEvolutionInstance($instanceModel->name, $instanceModel->phonenumber);
            } else {
                switch ($instanceStateOnEvolution['data']['state']) {
                    case 'connecting':
                        $result = $this->logoutInstance($instanceName);
                        // desloga e loga dnv
                        $instanceData = $this->evolutionInstanceService->connectInstance($instanceName);
                        break;
                    case 'close':
                        $instanceData = $this->evolutionInstanceService->connectInstance($instanceName);
                        break;
                    case 'open':
                        return $this->errorResponse('Instance already opened.');
                        break;
                    default:
                        break;
                }
            }
            // dd($instanceData);
            if (empty($instanceData['base64'])) return $this->errorResponse("Erro ao obter QrCode.");

            if (!empty($instanceModel->qrcode_path)) {
                // se existir qrcode no banco...
                // remove existente qrcode;
                Storage::delete('public/' . $instanceModel->qrcode_path);
                $instanceModel->qrcode_path = '';
                $instanceModel->save();
            }

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

            return $this->successResponse(['filename' => $filename]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    function getInstanceState($instanceName)
    {
        // can be -> open | close |
        if (empty($instanceName)) return [
            "error" => true,
            "message" => "Invalid parameter"
        ];

        $result = $this->evolutionInstanceService->getStateInstance($instanceName);
        if ($result["error"] == true) {
            return false;
        }

        return [
            'error' => false,
            'data' => [
                'state' => $result["data"]["state"]
            ]
        ];
    }
}
