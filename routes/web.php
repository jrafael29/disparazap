<?php

use App\Helpers\Base64ToFile;
use App\Http\Controllers\Webhook;
use App\Jobs\GetReadyFlowsToSentJob;
use App\Livewire\Pages\Auth\Register;
use App\Livewire\Pages\Auth\Login;
use App\Livewire\Pages\Flow\Index as FlowIndex;
use App\Livewire\Pages\Flow\Message\Index as MessageIndex;
use App\Livewire\Pages\Flow\Sent\Index as SentIndex;
use App\Livewire\Pages\Extractor\Index as ExtractorIndex;
use App\Livewire\Pages\Instance\Index as InstanceIndex;
use App\Livewire\Pages\Contact\Index as ContactIndex;

use App\Livewire\Pages\Admin\User\Index as AdminUserPage;



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
    // Route::get('/register', Register::class)->name('register');
    Route::get('/register', function () {
        return redirect()->to('/login');
    })->name('register');

    Route::get('/login', Login::class)->name('login');
});

// user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/logout', function () {
        Auth::logout();
        return redirect()->to('/');
    });
    Route::get('/home', Home::class)->name('home');
    Route::get('/instance', InstanceIndex::class)->name('instance');
    Route::get('/message-flow', FlowIndex::class)->name('flow');
    Route::get('/message-flow/{flow}/message', MessageIndex::class)->name('flow.message');
    Route::get('/message-flow/{flow}/sent', SentIndex::class)->name('flow.sent');
    Route::get('/extractor', ExtractorIndex::class)->name('extractor');

    Route::get('/contacts', ContactIndex::class)->name('contact');
});

// admin routes
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/users', AdminUserPage::class)->name('admin.user');
});



Route::get('/testando', function () {
    GetReadyFlowsToSentJob::dispatch()->onQueue('myqueue');
    return "testando";
});


Route::post('/webhook', [Webhook::class, 'webhookHandle']);
