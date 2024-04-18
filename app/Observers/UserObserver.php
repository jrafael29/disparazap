<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserCredit;
use App\Service\UserCreditService;
use Illuminate\Support\Facades\App;

class UserObserver
{
    private UserCreditService $userCreditService;
    public function __construct()
    {
        $this->userCreditService = App::make(UserCreditService::class);
    }

    public function created(User $user): void
    {
        $credit = 0;
        if (env('USER_INITIAL_CREDITS')) {
            $credit = (int) env('USER_INITIAL_CREDITS');
        }
        // bonus de boas vindas expira em 15 dias.
        $this->userCreditService->credit(
            userId: $user->id,
            credit: $credit,
            description: "Bônus de boas vindas",
            expireDays: 15
        );
    }
}
