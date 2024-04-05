<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sent extends Model
{
    use HasFactory;

    protected $table = 'sents';
    protected $fillable = ['user_id', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flows()
    {
        return $this->hasMany(FlowToSent::class);
    }
}
