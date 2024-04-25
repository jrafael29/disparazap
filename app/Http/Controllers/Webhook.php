<?php

namespace App\Http\Controllers;

use App\Service\Evolution\EvolutionInstanceService;
use Illuminate\Http\Request;
use App\Helpers\Base64ToFile;
use App\Jobs\InstanceOpenHandleJob;
use App\Models\Instance;
use App\Service\InstanceService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class Webhook extends Controller
{
    function __construct(
        private InstanceService $instanceService
    ) {
    }

    function webhookHandle(Request $request)
    {
        try {
            $requestBody = $request->all();

            $event = $requestBody['event'];

            switch ($event) {
                case 'qrcode.updated':
                    $this->qrCodeUpdatedHandle($requestBody);
                    break;
                case 'connection.update':
                    $this->connectionUpdateHandle($requestBody);
                    break;
            }

            // $filename = 'qrcode-updated-' . uniqid() . '.txt';
            // File::put($filename, json_encode($body));
        } catch (\Exception $e) {
            $filename = 'error-' . uniqid() . '.txt';
            File::put($filename, $e->getMessage());
            report($e);
        }
    }


    private function qrCodeUpdatedHandle($data)
    {
        $newBase64 = $data['data']['qrcode']['base64'];
        $instanceName = $data['instance'];
        $instanceModel = Instance::query()->where('name', $instanceName)->first();
        if (!empty($instanceModel->qrcode_path)) {
            Storage::delete('public/' . $instanceModel->qrcode_path);
        }
        $filename = 'qrcodes/qr_' . uniqid() . '.png';
        $storedFilename = Base64ToFile::storeImageFromBase64($newBase64, $filename);
        if ($storedFilename) {
            $instanceModel->qrcode_path = $storedFilename;
            $instanceModel->save();
        }

        $filename = 'qrcode-updated-' . uniqid() . '.txt';
    }

    private function connectionUpdateHandle($data)
    {
        try {
            Log::info("iniciou", $data);
            $state = $data['data']['state'];
            $instanceName = $data['instance'];
            $instanceModel = Instance::query()->where('name', $instanceName)->first();

            if (!$instanceModel) return false;

            if ($state === 'open') {
                Log::info('abriu', $data);
                InstanceOpenHandleJob::dispatch($instanceModel)->onQueue('low');
            }

            if ($state === 'close') {
                $instanceModel->online = false;
                $instanceModel->save();
            }
            return true;
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
    }
}
