<?php

namespace App\Observers;

use App\Models\User;
use App\Service\UserWalletService;
use Illuminate\Support\Facades\App;

class UserObserver
{
    private UserWalletService $userWalletService;
    public function __construct()
    {
        $this->userWalletService = App::make(UserWalletService::class);
    }

    public function created(User $user): void
    {
        $credit = 0;
        if (env('USER_INITIAL_CREDITS')) {
            $credit = (int) env('USER_INITIAL_CREDITS');
        }
        // bonus de boas vindas expira em 15 dias.
        $this->userWalletService->credit(
            userId: $user->id,
            credit: $credit,
            description: "Bônus de boas vindas",
            expireDays: 15
        );
    }
}
