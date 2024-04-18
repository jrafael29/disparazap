<?php

namespace App\Service;

use App\Models\User;
use App\Models\UserBalanceHistory;
use App\Models\UserCredit;
use App\Traits\ServiceResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserCreditService
{
    use ServiceResponseTrait;
    public function credit($userId, $credit, $description, $expireDays = 0)
    {
        try {
            DB::beginTransaction();
            $userCredit = UserCredit::query()
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
            $userBalanceHistory = UserBalanceHistory::query()->create([
                'user_id' => $userId,
                'operation' => 'credit',
                'last_credit_amount'  => $userCredit->credit,
                'amount' => $credit,
                'description' => $description,
                'expires_at' => $expiresAt
            ]);
            $userCredit->increment('credit', $credit);
            $userCredit->save();
            DB::commit();
            return $this->successResponse([
                'operation' => 'credit',
                'userCredit' => $userCredit,
                'history' => $userBalanceHistory,
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
            $userCredit = UserCredit::query()
                ->firstOrCreate(
                    ['user_id' => $userId],
                    ['user_id' => $userId, 'credit' => 0]
                );
            $userBalanceHistory = UserBalanceHistory::query()->create([
                'user_id' => $userId,
                'operation' => 'debit',
                'last_credit_amount'  => $userCredit->credit,
                'amount' => $value,
                'description' => $description
            ]);
            $userCredit->decrement('credit', $value);
            $userCredit->save();
            DB::commit();
            return $this->successResponse([
                'operation' => 'debit',
                'userCredit' => $userCredit,
                'history' => $userBalanceHistory,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            dd("", $e);
        }
    }
}
