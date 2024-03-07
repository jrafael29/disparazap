<?php

use App\Helpers\Base64ToFile;
use App\Livewire\Pages\Auth\Register;
use App\Livewire\Pages\Auth\Login;
use App\Livewire\Pages\Flow\Index as FlowIndex;
use App\Livewire\Pages\Instance\Index as InstanceIndex;


use App\Livewire\Pages\Home;
use App\Livewire\Welcome;
use App\Models\Instance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', Welcome::class);

Route::middleware(['guest'])->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', function () {
        Auth::logout();
        return redirect()->to('/');
    });
    Route::get('/home', Home::class)->name('home');

    Route::get('/instance', InstanceIndex::class)->name('instance');
    Route::get('/message-flow', FlowIndex::class)->name('flow');
});


// Route::post('/updated-qrcode/webhook', function (Request $request) {

//     try {
//         $body = $request->all();


//         // $filename = 'test' . uniqid() . '.txt';
//         // File::put($filename, json_encode($body));


//         $instanceName = $body['instance'];
//         $qrCodeBase64 = $body['data']['qrcode']['base64'];

//         $instanceModel = Instance::query()->where('name', $instanceName)->first();

//         if (!empty($instanceModel->qrcode_path)) {
//             // remover imagem existente
//             Storage::delete('public/' . $instanceModel->qrcode_path);
//         }
//         $filename = 'qrcodes/qr_' . uniqid() . '.png';
//         $storedFilename = Base64ToFile::storeImageFromBase64($qrCodeBase64, $filename);
//         if ($storedFilename) {
//             $instanceModel->qrcode_path = $storedFilename;
//             $instanceModel->save();
//         }
//     } catch (\Exception $e) {
//         $filename = 'error-' . uniqid() . '.txt';
//         File::put($filename, $e->getMessage());
//         report($e);
//     }
// });

Route::post('/webhook', function (Request $request) {
    try {
        $body = $request->all();

        $event = $body['event'];

        switch ($event) {
            case 'qrcode.updated':
                $newBase64 = $body['data']['qrcode']['base64'];
                $instanceName = $body['instance'];
                $instanceModel = Instance::query()->where('name', $instanceName)->first();
                if (!empty($instanceModel->qrcode_path)) {
                    // remover imagem existente
                    Storage::delete('public/' . $instanceModel->qrcode_path);
                }
                $filename = 'qrcodes/qr_' . uniqid() . '.png';
                $storedFilename = Base64ToFile::storeImageFromBase64($newBase64, $filename);
                if ($storedFilename) {
                    $instanceModel->qrcode_path = $storedFilename;
                    $instanceModel->save();
                }

                $filename = 'qrcode-updated-' . uniqid() . '.txt';
                break;
            case 'connection.update':

                $state = $body['data']['state'];
                $instanceName = $body['instance'];
                $instanceModel = Instance::query()->where('name', $instanceName)->first();

                if ($state === 'open') {
                    $instanceModel->online = true;
                    $instanceModel->save();
                }

                $filename = 'connection-update-' . uniqid() . '.txt';
                break;
        }


        File::put($filename, json_encode($body));
    } catch (\Exception $e) {
        $filename = 'error-' . uniqid() . '.txt';
        File::put($filename, $e->getMessage());
        report($e);
    }
});
