<?php

namespace App\Repository;

use App\Models\Sent;

class SentRepository
{

    public function pauseSent($sentId)
    {
        $sent = Sent::findOrFail($sentId);
        $sent->paused = 1;
        $sent->save();
        return true;
    }

    public function playSent($sentId)
    {
        $sent = Sent::findOrFail($sentId);
        $sent->paused = 0;
        $sent->save();
        return true;
    }
}
