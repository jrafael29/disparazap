<?php

namespace App\Models;

use App\Observers\MessageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(MessageObserver::class)]
class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'flow_id',
        'type_id',
        'text',
        'filepath',
        'position',
        'delay'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(MessageType::class, 'type_id', 'id');
    }
}
