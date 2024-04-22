<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'isAdmin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function wallet()
    {
        return $this->hasOne(UserWallet::class, 'user_id', 'id');
    }

    public function flowToSent()
    {
        return $this->hasMany(FlowToSent::class, 'user_ud', 'id');
    }

    public function instances()
    {
        return $this->hasMany(Instance::class, 'user_id', 'id');
    }

    public function checks()
    {
        return $this->hasMany(PhonenumberCheck::class, 'user_id', 'id');
    }

    public function contacts()
    {
        return $this->hasManyThrough(
            related: Contact::class,
            through: UserContact::class,
            firstKey: 'user_id',
            secondKey: 'id',
            localKey: 'id',
            secondLocalKey: 'contact_id'
        );
    }
}
