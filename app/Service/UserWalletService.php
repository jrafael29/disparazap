<?php

namespace App\Service;

use App\Models\User;
use App\Models\UserWalletHistory;
use App\Models\UserWallet;
use App\Traits\ServiceResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserWalletService
{
    use ServiceResponseTrait;
    public function credit($userId, $credit, $description, $expireDays = 0)
    {
        try {
            DB::beginTransaction();
            $userWallet = UserWallet::query()
                ->firstOrCreate(
                    ['user_id' => $userId],
                    ['user_id' => $userId, 'credit' => 0]
                );
            $expireCreditDays = 0;
            if ($expireDays > 0) {
                $expireCreditDays = $expireDays;
            } else {
                $expireCreditDays = (int) env('USER_CREDIT_EXPIRE_DAYS') ?? 1;
            }
            $expiresAt = Carbon::now()->addDays($expireCreditDays);
            $userWalletHistory = UserWalletHistory::query()->create([
                'user_id' => $userId,
                'operation' => 'credit',
                'last_credit_amount'  => $userWallet->credit,
                'amount' => $credit,
                'description' => $description,
                'expires_at' => $expiresAt
            ]);
            $userWallet->increment('credit', $credit);
            $userWallet->save();
            DB::commit();
            return $this->successResponse([
                'operation' => 'credit',
                'wallet' => $userWallet,
                'history' => $userWalletHistory,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            dd("", $e);
        }
    }

    public function debitOne($userId, $description)
    {
        return $this->debit(
            userId: $userId,
            value: 1,
            description: $description
        );
    }

    public function debit($userId, $value, $description)
    {
        try {
            DB::beginTransaction();
            $userWallet = UserWallet::query()
                ->firstOrCreate(
                    ['user_id' => $userId],
                    ['user_id' => $userId, 'credit' => 0]
                );
            $userWalletHistory = UserWalletHistory::query()->create([
                'user_id' => $userId,
                'operation' => 'debit',
                'last_credit_amount'  => $userWallet->credit,
                'amount' => $value,
                'description' => $description
            ]);
            $userWallet->decrement('credit', $value);
            $userWallet->save();
            DB::commit();
            return $this->successResponse([
                'operation' => 'debit',
                'wallet' => $userWallet,
                'history' => $userWalletHistory,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            dd("", $e);
        }
    }
}
