<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowToSent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'flow_id',
        'instance_id',
        "to",
        "sent",
        "busy",
        "to_sent_at",
        'delay_in_seconds',
    ];
}
