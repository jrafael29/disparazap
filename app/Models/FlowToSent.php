<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowToSent extends Model
{
    use HasFactory;

    protected $table = 'flow_to_sents';

    protected $fillable = [
        'user_id',
        'flow_id',
        'instance_id',
        'sent_id',
        'contact_id',
        'user_historic_credit_id',
        "to",
        "sent",
        "busy",
        "to_sent_at",
        'delay_in_seconds',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function flow()
    {
        return $this->belongsTo(MessageFlow::class);
    }
    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }
    public function sent()
    {
        return $this->belongsTo(Sent::class);
    }
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
    public function historic()
    {
        return $this->belongsTo(UserBalanceHistory::class);
    }
}
