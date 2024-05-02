<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserGroup;
use App\Service\UserGroupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class AddContactsToGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private UserGroupService $userGroupService;
    private User $user;
    private UserGroup $userGroup;
    private $phonenumbers;
    /**
     * Create a new job instance.
     */
    public function __construct($userId, $groupId, $phonenumbers = [])
    {
        $this->phonenumbers = $phonenumbers;
        $this->user = User::query()->find($userId);
        $this->userGroup = UserGroup::query()->find($groupId);
        $this->userGroupService = App::make(UserGroupService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $result = $this->userGroupService->addContactsToGroup(
                userId: $this->user->id,
                groupId: $this->userGroup->id,
                contacts: $this->phonenumbers
            );
        } catch (\Exception $e) {
            Log::error("error: AddContactsToGroupJob", ['message' => $e->getMessage()]);
        }
    }
}
