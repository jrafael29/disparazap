<?php

namespace App\Jobs;

use App\Models\User;
use App\Service\UserContactService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class StoreContactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private UserContactService $userContactService;
    private User $user;
    private $phonenumbers;
    /**
     * Create a new job instance.
     */
    public function __construct($userId, $phonenumbers = [])
    {
        $this->phonenumbers = $phonenumbers;
        $this->user = User::query()->find($userId);
        $this->userContactService = App::make(UserContactService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $this->userContactService->createManyUserContacts(
            userId: $this->user->id,
            phonenumbers: $this->phonenumbers
        );
    }
}
