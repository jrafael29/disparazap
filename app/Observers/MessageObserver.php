<?php

namespace App\Observers;

use App\Models\Message;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        $lastMessageByPosition = Message::query()->where('id', "<>", $message->id)->orderBy('position', 'desc')->first();
        // dd($lastMessageByPosition);
        if ($lastMessageByPosition) {
            $message->position = $lastMessageByPosition->position + 1;
            $message->save();
        }
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "deleted" event.
     */
    public function deleted(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "restored" event.
     */
    public function restored(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "force deleted" event.
     */
    public function forceDeleted(Message $message): void
    {
        //
    }
}
