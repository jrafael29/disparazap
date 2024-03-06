<?php

use App\Livewire\Pages\Auth\Register;
use App\Livewire\Pages\Auth\Login;
use App\Livewire\Pages\Flow\Index as FlowIndex;

use App\Livewire\Pages\Home;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;

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
    Route::get('/home', Home::class)->name('home');

    Route::get('/message-flow', FlowIndex::class)->name('flow');
});
