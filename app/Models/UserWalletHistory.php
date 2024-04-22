<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWalletHistory extends Model
{
    use HasFactory;

    protected $table = 'user_wallet_histories';

    protected $fillable = ['user_id', 'operation', 'last_credit_amount', 'amount', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
