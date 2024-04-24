<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'phonenumber',
        'online',
        'token',
        'active',
        'qrcode_path',
        'verified_at',
        'available_at'
    ];

    protected $casts = [
        'online' => 'boolean',
        'active' => 'boolean',
        'verified_at' => 'datetime',
        'available_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function flowToSent()
    {
        return $this->hasMany(FlowToSent::class);
    }
}
